<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Todo;
use Illuminate\Support\Carbon;

new class extends Component {
    public $todos = [];

    #[Validate('required|min:3')]
    public $title = '';

    public $status = 'pending';

    #[Validate('boolean')]
    public $priority = false;

    #[Validate('nullable|date')]
    public $due_at = '';

    public bool $hideCompleted = false;

    public function mount(): void
    {
        $this->loadTodos();
    }

    public function updatedHideCompleted(): void
    {
        $this->loadTodos();
    }

    public function save(): void
    {
        $this->validate();

        Todo::create([
            'title' => $this->title,
            'completed' => $this->status === 'completed',
            'priority' => $this->priority,
            'status' => $this->status,
            'due_at' => $this->due_at ? Carbon::parse($this->due_at) : null,
        ]);

        $this->reset('title', 'priority', 'due_at');
        $this->status = 'pending';

        $this->loadTodos();
    }

    public function delete($id): void
    {
        Todo::findOrFail($id)->delete();

        $this->loadTodos();
    }

    public function togglePriority($id): void
    {
        $todo = Todo::findOrFail($id);
        $todo->priority = ! $todo->priority;
        $todo->save();

        $this->loadTodos();
    }

    public function updateStatus($id, ?string $status = null): void
    {
        if (! in_array($status, ['not_yet', 'pending', 'completed', 'cancelled'], true)) {
            return;
        }

        $todo = Todo::findOrFail($id);
        $todo->status = $status;
        $todo->completed = $status === 'completed';
        $todo->save();

        $this->loadTodos();
    }

    protected function loadTodos(): void
    {
        $query = Todo::query();

        if ($this->hideCompleted) {
            $query->where('status', '!=', 'completed');
        }

        $this->todos = $query
            ->orderByRaw("CASE WHEN status IN ('not_yet', 'pending') THEN 0 WHEN status = 'completed' THEN 1 ELSE 2 END")
            ->orderByDesc('priority')
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->orderByDesc('created_at')
            ->get();
    }
};
?>

<div class="todo-app">
    <div class="todo-shell">
        <div class="window">
            <div class="window-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-amber"></span>
                <span class="dot dot-green"></span>
                <span class="window-title">todo@livewire — ~/tasks</span>
                <label class="hide-completed-toggle">
                    <input type="checkbox" wire:model.live="hideCompleted">
                    <span>hide done</span>
                </label>
            </div>

            <div class="window-body">
                <form wire:submit="save" class="prompt-form">
                    <span class="prompt-symbol">&gt;</span>
                    <input type="text" wire:model.live="title" class="prompt-input" placeholder="log a new task..." autocomplete="off">
                    <input type="datetime-local" wire:model.live="due_at" class="prompt-date" aria-label="Due date and time">
                    <label class="priority-create-toggle">
                        <input type="checkbox" wire:model.live="priority">
                        <span>star</span>
                    </label>
                    <button type="submit" class="prompt-submit">enter</button>
                </form>
                @error('title') <div class="prompt-error">{{ $message }}</div> @enderror
                @error('due_at') <div class="prompt-error">{{ $message }}</div> @enderror

                <ul class="task-log">
                    @forelse ($todos as $index => $todo)
                        <li class="task-line {{ $todo->completed ? 'is-done' : '' }} {{ $todo->priority ? 'is-priority' : '' }} {{ $todo->status === 'cancelled' ? 'is-cancelled' : '' }}" wire:key="todo-{{ $todo->id }}">
                            <button type="button" wire:click="togglePriority({{ $todo->id }})" class="priority-toggle {{ $todo->priority ? 'is-active' : '' }}" aria-label="Toggle priority">
                                {{ $todo->priority ? '★' : '☆' }}
                            </button>

                            <span class="line-index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>

                            <div class="task-copy">
                                <div class="task-headline">
                                    <span class="line-text">{{ $todo->title }}</span>
                                </div>

                                <div class="task-meta">
                                    @if ($todo->due_at)
                                        <span class="task-due {{ $todo->due_at->isPast() && ! in_array($todo->status, ['completed', 'cancelled']) ? 'is-overdue' : '' }}">
                                            due {{ $todo->due_at->format('M j, g:i A') }}
                                            @if ($todo->due_at->isPast() && ! in_array($todo->status, ['completed', 'cancelled']))
                                                <span class="task-overdue">overdue</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="task-due task-due--empty">no due date</span>
                                    @endif
                                </div>
                            </div>

                            <div class="task-controls">
                                <select wire:change="updateStatus({{ $todo->id }}, $event.target.value)" class="status-select status-select--{{ $todo->status }}" aria-label="Update task status">
                                    <option value="not_yet" @selected($todo->status === 'not_yet')>not yet</option>
                                    <option value="pending" @selected($todo->status === 'pending')>pending</option>
                                    <option value="completed" @selected($todo->status === 'completed')>completed</option>
                                    <option value="cancelled" @selected($todo->status === 'cancelled')>cancelled</option>
                                </select>

                                <button wire:click="delete({{ $todo->id }})" type="button" class="line-delete" aria-label="Delete task">×</button>
                            </div>
                        </li>
                    @empty
                        <li class="task-empty">No tasks logged yet — add one above.</li>
                    @endforelse
                </ul>

                <div class="status-bar">
                    <span>{{ count($todos) }} total</span>
                    <span class="status-dot">·</span>
                    <span>{{ collect($todos)->where('completed', true)->count() }} done</span>
                    <span class="status-dot">·</span>
                    <span>{{ collect($todos)->where('completed', false)->count() }} active</span>
                </div>
            </div>
        </div>
    </div>
</div>