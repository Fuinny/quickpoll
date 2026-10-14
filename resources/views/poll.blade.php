<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quick Eat Poll</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { background: #f4f5f7; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }

        .poll-card { background: white; padding: 28px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); width: 100%; max-width: 420px; }
        h2 { margin-bottom: 20px; font-size: 1.3rem; color: #1e293b; text-align: center; }

        /* Voting buttons mode */
        .vote-btn {
            width: 100%;
            padding: 14px 18px;
            margin-bottom: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }
        .vote-btn:hover { border-color: #3b82f6; background: #eff6ff; transform: translateY(-1px); }

        /* Results mode (Bar / Columns) */
        .result-row { margin-bottom: 14px; }
        .result-label { display: flex; justify-content: space-between; font-size: 0.95rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .bar-container { background: #e2e8f0; border-radius: 8px; height: 32px; overflow: hidden; position: relative; }
        .bar-fill { height: 100%; background: #3b82f6; width: 0%; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 8px; }
        .user-picked .bar-fill { background: #10b981; } /* Highlight user selection green */

        .vote-count-text { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 0.85rem; font-weight: bold; color: #1e293b; }

        .reset-btn { margin-top: 20px; background: none; border: none; color: #94a3b8; font-size: 0.8rem; width: 100%; cursor: pointer; text-decoration: underline; }
        .reset-btn:hover { color: #64748b; }
    </style>
</head>
<body>

    <div class="poll-card">
        <h2 id="question">What should we eat?</h2>
        <div id="poll-body"></div>
        <button class="reset-btn" onclick="resetVote()">Reset local vote (Testing)</button>
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
                    row.className = `result-row ${isUserPick ? 'user-picked' : ''}`;
                    row.innerHTML = `
                        <div class="result-label">
                            <span>${opt.text} ${isUserPick ? '✓ (You)' : ''}</span>
                            <span>${percent}%</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: ${percent}%;"></div>
                            <span class="vote-count-text">${opt.votes} vote${opt.votes !== 1 ? 's' : ''}</span>
                        </div>
                    `;
                    container.appendChild(row);
                });

            } else {
                // --- DISPLAY VOTING BUTTONS ---
                poll.options.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className = 'vote-btn';
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
            // Revert user's vote count if they reset for testing
            render();
        }

        // Initial load
        render();
    </script>

</body>
</html>
