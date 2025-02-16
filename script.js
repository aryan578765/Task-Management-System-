// Example: Client-side form validation
function validateTaskForm() {
    const title = document.getElementById('title').value.trim();
    const dueDate = document.getElementById('due_date').value;

    if (title === "") {
        alert("Title is required.");
        return false;
    }

    if (dueDate === "") {
        alert("Due date is required.");
        return false;
    }

    return true;
}

// Dynamic form validation and interactions
document.getElementById('title').addEventListener('input', function() {
    const titleLength = this.value.length;
    const messageElement = document.getElementById('titleLengthMessage');
    messageElement.textContent = `Title length: ${titleLength}`;
    if (titleLength < 3) {
        messageElement.style.color = 'red';
    } else {
        messageElement.style.color = 'green';
    }
});