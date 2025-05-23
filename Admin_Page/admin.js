const hamburger = document.querySelector(".toggle-btn");
const toggler = document.querySelector("#icon");
hamburger.addEventListener("click", function() {
    document.querySelector("#sidebar").classList.toggle("expand");
    toggler.classList.toggle("bxs-chevrons-right");
    toggler.classList.toggle("bxs-chevrons-left");
});

// User Management Functions
function viewUser(userId) {
    window.location.href = `admin.php?manage_users&view=${userId}`;
}

function toggleUserStatus(userId, currentStatus) {
    if (confirm(`Are you sure you want to ${currentStatus === 'active' ? 'deactivate' : 'activate'} this user?`)) {
        fetch('user_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle_status&user_id=${userId}&current_status=${currentStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch('user_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// The chart will be initialized from admin.php with actual data
const initializeChart = (chartData) => {
    new Chart(document.getElementById("bar-chart-grouped"), {
        type: 'bar',
        data: {
            labels: chartData.years,
            datasets: chartData.categories.map((category, index) => ({
                label: category.name,
                backgroundColor: [
                    '#3e95cd',
                    '#8e5ea2',
                    '#3cba9f',
                    '#e8c3b9',
                    '#c45850'
                ][index % 5],
                data: category.data
            }))
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Document Uploads by Category and Year'
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Documents'
                    }
                }
            }
        }
    });
};