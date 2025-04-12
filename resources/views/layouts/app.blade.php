<!DOCTYPE html>
<html lang="ro">
<meta name="csrf-token" content="{{ csrf_token() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@stack('scripts')
<body class="antialiased text-gray-900">
    @yield('content')
</body>
<script>
    function quizApp() {
        return {
            currentQuestion: 0,
            selectedAnswer: null,
            score: 0,
            showAnswer: false,
            questions: [
                {
                    text: "Ce este un buget personal?",
                    options: ["Plan pentru cheltuieli și venituri", "Card de credit", "Cont de economii", "Împrumut bancar"],
                    correct: 0,
                },
                {
                    text: "Este bine să economisești lunar un procent fix din venituri?",
                    options: ["Da", "Nu"],
                    correct: 0,
                },
                {
                    text: "Un card de credit este același lucru cu un card de debit.",
                    options: ["Adevărat", "Fals"],
                    correct: 1,
                }
            ],
            selectAnswer(index) {
                this.selectedAnswer = index;
                this.showAnswer = true;
                if (index === this.questions[this.currentQuestion].correct) {
                    this.score++;
                }
            },
            nextQuestion() {
                this.currentQuestion++;
                this.selectedAnswer = null;
                this.showAnswer = false;
            },
            getFeedback() {
                if (this.score <= 4) return "🟡 Nivel de bază – mai ai de învățat!";
                if (this.score <= 7) return "🟠 Nivel mediu – ești pe drumul cel bun!";
                return "🟢 Nivel avansat – bravo!";
            },
            shareToFeed() {
                fetch('/quiz/share', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        score: this.score
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    alert('✅ Scorul tău a fost partajat pe feed!');
                })
                .catch(err => {
                    console.error(err);
                    alert('Eroare la partajare.');
                })
                .then(data => {
                    window.location.href = '/dashboard';
                });
            }
        }
    }
    </script>
<script src="https://unpkg.com/alpinejs" defer></script>
</html>
