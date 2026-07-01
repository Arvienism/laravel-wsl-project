<?php

use Livewire\Component;
use App\Models\Todo;

new class extends Component {
    public $todos = [];

    public function mount()
    {
        $this->todos = Todo::latest()->get();
    }
};
?>

<div>
    <h1> Todo List</h1>

    <ul>
        @foreach ($todos as $todo)
            <li>{{ $todo->title }}</li>
        @endforeach
    </ul>
</div>