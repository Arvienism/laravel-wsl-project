<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Todo;

new class extends Component {
    public $todos = [];

    #[Validate('required|min:3')]
    public $title = '';

    public function mount()
    {
        $this->todos = Todo::latest()->get();
    }

    public function save()
    {
        $this->validate();

        Todo::create([
            'title' => $this->title,
            'completed' => false,
        ]);

        $this->reset('title');
        $this->todos = Todo::latest()->get();
    }

    public function delete($id)
    {
        Todo::find($id)->delete();
        $this->todos = Todo::latest()->get();
    }

    public function toggle($id)
    {
        $todo = Todo::find($id);
        $todo->completed = !$todo->completed;
        $todo->save();

        $this->todos = Todo::latest()->get();
    }
};
?>

<div class="todo-app">
    <div class="window">
        <div class="window-bar">
            <span class="dot dot-red"></span>
            <span class="dot dot-amber"></span>
            <span class="dot dot-green"></span>
            <span class="window-title">todo@livewire — ~/tasks</span>
        </div>

        <div class="window-body">
            <form wire:submit="save" class="prompt-form">
                <span class="prompt-symbol">&gt;</span>
                <input type="text" wire:model.live="title" class="prompt-input" placeholder="log a new task..." autocomplete="off">
                <button type="submit" class="prompt-submit">enter</button>
            </form>
            @error('title') <div class="prompt-error">{{ $message }}</div> @enderror

            <ul class="task-log">
                @forelse ($todos as $index => $todo)
                    <li class="task-line {{ $todo->completed ? 'is-done' : '' }}" wire:key="todo-{{ $todo->id }}">
                        <span class="line-index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <label class="line-toggle">
                            <input type="checkbox" wire:click="toggle({{ $todo->id }})" @checked($todo->completed)>
                            <span class="checkmark"></span>
                        </label>
                        <span class="line-text">{{ $todo->title }}</span>
                        <button wire:click="delete({{ $todo->id }})" class="line-delete" aria-label="Delete task">×</button>
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
                <span>{{ collect($todos)->where('completed', false)->count() }} pending</span>
            </div>
        </div>
    </div>
</div>