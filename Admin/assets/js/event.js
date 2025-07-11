        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<span class="text-gray-400">No image selected</span>';
            }
        }

        function addTicketType() {
            const container = document.getElementById('ticketTypesContainer');
            const row = document.createElement('div');
            row.className = 'ticket-type-row';
            row.innerHTML = `
                <input type="text" name="ticket_types[]" placeholder="Ticket Type (e.g. VIP, Regular)" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                <input type="number" name="ticket_prices[]" placeholder="Price" min="0" step="0.01" class="w-32 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-200 focus:outline-none transition" required>
                <button type="button" class="remove-ticket-btn" onclick="removeTicketType(this)">Remove</button>
            `;
            container.appendChild(row);
            updateRemoveButtons();
        }

        function removeTicketType(btn) {
            btn.parentElement.remove();
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('#ticketTypesContainer .ticket-type-row');
            rows.forEach((row, idx) => {
                const btn = row.querySelector('.remove-ticket-btn');
                btn.classList.toggle('hidden', rows.length === 1);
            });
        }
        window.onload = updateRemoveButtons;
