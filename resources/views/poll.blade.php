<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quick Eat Poll</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100 p-3">

    <div class="card shadow border-0 p-4 w-100" style="max-width: 420px; border-radius: 16px;">
        <h2 id="question" class="fs-5 fw-bold text-dark text-center mb-4">What should we eat?</h2>
        <div id="poll-body"></div>
        <button class="btn btn-link text-muted text-decoration-underline w-100 mt-3 p-0 shadow-none" style="font-size: 0.8rem;" onclick="resetVote()">Reset local vote (Testing)</button>
    </div>

    <script>
        // Hardcoded initial poll state
        const POLL_ID = "eat_poll_001";
        let poll = {
            question: "What should we eat?",
            options: [
                { id: 'pizza', text: "Pizza 🍕", votes: 5 },
                { id: 'sushi', text: "Sushi 🍣", votes: 8 },
                { id: 'tacos', text: "Tacos 🌮", votes: 3 },
                { id: 'burgers', text: "Burgers 🍔", votes: 4 }
            ]
        };

        const container = document.getElementById('poll-body');

        function render() {
            const userChoice = localStorage.getItem('voted_' + POLL_ID);
            container.innerHTML = '';

            if (userChoice) {
                // --- DISPLAY RESULTS ---
                const totalVotes = poll.options.reduce((sum, opt) => sum + opt.votes, 0);

                poll.options.forEach(opt => {
                    const percent = totalVotes > 0 ? Math.round((opt.votes / totalVotes) * 100) : 0;
                    const isUserPick = opt.id === userChoice;

                    const row = document.createElement('div');
                    row.className = 'mb-3';
                    row.innerHTML = `
                        <div class="d-flex justify-content-between fw-semibold text-secondary mb-1" style="font-size: 0.95rem;">
                            <span>${opt.text} ${isUserPick ? '<span class="text-success fw-bold">✓ (You)</span>' : ''}</span>
                            <span>${percent}%</span>
                        </div>
                        <div class="progress bg-secondary-subtle position-relative" style="height: 32px; border-radius: 8px;">
                            <div class="progress-bar ${isUserPick ? 'bg-success' : 'bg-primary'}"
                                 style="width: ${percent}%; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 8px;">
                            </div>
                            <span class="position-absolute end-0 top-50 translate-middle-y pe-3 text-dark fw-bold" style="font-size: 0.85rem;">
                                ${opt.votes} vote${opt.votes !== 1 ? 's' : ''}
                            </span>
                        </div>
                    `;
                    container.appendChild(row);
                });

            } else {
                // --- DISPLAY VOTING BUTTONS ---
                poll.options.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-outline-secondary w-100 text-start py-3 px-3 mb-2 fw-semibold rounded-3';
                    btn.style.borderColor = '#e2e8f0';
                    btn.innerText = opt.text;
                    btn.onclick = () => castVote(opt.id);
                    container.appendChild(btn);
                });
            }
        }

        function castVote(optionId) {
            // Find option and bump vote count
            const option = poll.options.find(o => o.id === optionId);
            if (option) option.votes += 1;

            // Lock user from voting again in this browser
            localStorage.setItem('voted_' + POLL_ID, optionId);

            render();
        }

        function resetVote() {
            localStorage.removeItem('voted_' + POLL_ID);
            render();
        }

        // Initial load
        render();
    </script>

</body>
</html>
