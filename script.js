document.getElementById("todoForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let title = document.getElementById("title").value.trim();
    let description = document.getElementById("description").value.trim();
    let priority = document.getElementById("priority").value; // Ambil nilai prioritas

    if (title === "" || description === "") {
        alert("Title and Description cannot be empty!");
        return;
    }

    let formData = new FormData();
    formData.append("title", title);
    formData.append("description", description);
    formData.append("priority", priority); // Tambahkan prioritas ke formData

    fetch("process.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Tampilkan pesan sukses atau error

        if (data.status === "success") {
            let taskList = document.getElementById("tasks");
            let newTask = document.createElement("li");
            newTask.setAttribute("id", "task-" + data.id);
            
            // Menambahkan class CSS berdasarkan prioritas
            let priorityClass = "";
            if (priority === "high") priorityClass = "priority-high";
            if (priority === "medium") priorityClass = "priority-medium";
            if (priority === "low") priorityClass = "priority-low";

            newTask.classList.add(priorityClass);
            newTask.innerHTML = `
                <p><strong>${title}</strong><br>${description}</p>
                <button class='edit-btn' onclick='editTask(${data.id})'>Edit</button>
                <button class='delete-btn' onclick='deleteTask(${data.id})'>Hapus</button>
            `;
            taskList.prepend(newTask);
            document.getElementById("todoForm").reset();
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menambahkan tugas.");
    });
});

// Fungsi untuk menghapus tugas
function deleteTask(id) {
    if (!confirm("Yakin mau hapus?")) return;
    
    fetch(`delete.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById(`task-${id}`).remove();
        } else {
            alert("Gagal menghapus tugas.");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menghapus tugas.");
    });
}
