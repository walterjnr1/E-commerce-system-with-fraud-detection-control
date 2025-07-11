        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('orderSearch');
            const table = document.getElementById('ordersTable');
            const tbody = table.querySelector('tbody');

            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const rows = tbody.querySelectorAll('tr');
                rows.forEach(row => {
                    let match = false;
                    for (let cell of row.cells) {
                        if (cell.textContent.toLowerCase().includes(query)) {
                            match = true;
                            break;
                        }
                    }
                    row.style.display = match ? '' : 'none';
                });
            });
        });
