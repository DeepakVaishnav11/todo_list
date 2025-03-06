@extends('layouts.app')
@section('content')
<div class="container mt-4">
    <div class="card p-3">
        <div class="d-flex align-items-center">
            <input type="checkbox" id="showAll" class="me-2" onclick="fetchTasks(this.checked)">
            <label for="showAll">Show All Tasks</label>
        </div>
        <div class="input-group mt-3">
            <input type="text" id="taskInput" class="form-control" placeholder="Project # To Do" onkeypress="handleEnter(event)">
            <button class="btn btn-success" onclick="addTask()">Add</button>
        </div>
        <ul class="list-group mt-3" id="taskList"></ul>
    </div>
</div>
<script>
async function fetchTasks(showAll = false) {
    let response = await fetch(`/api/tasks?showAll=${showAll}`);
    let tasks = await response.json();
    let taskList = document.getElementById('taskList');
    taskList.innerHTML = '';
    tasks.forEach(task => {
        let li = document.createElement('li');
        li.classList.add('list-group-item', 'd-flex', 'align-items-center', 'justify-content-between');
        li.innerHTML = `
            <div class="d-flex align-items-center">
                <input type='checkbox' class='me-2' ${task.completed ? 'checked' : ''} onchange='toggleTask(${task.id}, this.checked)'>
                <span ${task.completed ? 'style="display:none;"' : ''}>${task.title}</span>
                <small class='text-muted ms-2'>a few seconds ago</small>
            </div>
            <div class="d-flex align-items-center">
                <img src='https://i.pravatar.cc/30' class='rounded-circle me-2' alt='User'>
                <button class='btn btn-danger btn-sm' onclick='deleteTask(${task.id})'>🗑</button>
            </div>`;
        taskList.appendChild(li);
    });
}
function handleEnter(event) {
    if (event.key === 'Enter') addTask();
}
async function addTask() {
    let title = document.getElementById('taskInput').value.trim();
    if (!title) {
        swal("Error", "Task cannot be empty!", "error");
        return;
    }

    let response = await fetch('/api/tasks', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title })
    });

    let result = await response.json();

    if (response.ok) {
        document.getElementById('taskInput').value = '';
        fetchTasks();
        swal("Success", "Task added successfully!", "success");
    } else {
        swal("Error", result.message, "error");
    }
}




async function toggleTask(id, completed) {
    await fetch(`/api/tasks/${id}`, {
        method: 'PUT',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ completed })
    });
    fetchTasks();
}
async function deleteTask(id) {
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this task!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(async (willDelete) => {
        if (willDelete) {
            await fetch(`/api/tasks/${id}`, { method: 'DELETE' });
            swal("Deleted!", "Your task has been deleted.", "success");
            fetchTasks();
        }
    });
}
fetchTasks();
</script>
@endsection