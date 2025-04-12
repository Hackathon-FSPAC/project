<!DOCTYPE html>
<html lang="ro">
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
                },
                {
                    text: "Ce înseamnă dobânda compusă?",
                    options: ["Dobândă doar pe suma inițială", "Dobândă și pe dobândă acumulată"],
                    correct: 1,
                },
                {
                    text: "Ce reprezintă scorul de credit?",
                    options: ["Vârsta ta", "Nivelul tău de educație", "Fiabilitatea ta de plată"],
                    correct: 2,
                },
                {
                    text: "Care este o regulă de bază în economisire?",
                    options: ["Cheltuie tot", "Plătește-te pe tine primul"],
                    correct: 1,
                },
                {
                    text: "O rată fixă înseamnă că...",
                    options: ["Dobânda poate varia", "Plătești aceeași sumă lunar"],
                    correct: 1,
                },
                {
                    text: "Un fond de urgență ar trebui să acopere...",
                    options: ["0 luni", "1 lună", "3–6 luni de cheltuieli"],
                    correct: 2,
                },
                {
                    text: "Ce e inflația?",
                    options: ["Scăderea prețurilor", "Creșterea valorii banilor", "Creșterea prețurilor"],
                    correct: 2,
                },
                {
                    text: "Este bine să ai un singur venit?",
                    options: ["Da", "Nu, diversificarea e importantă"],
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
            generateShareLink() {
                const text = `Am obținut ${this.score}/10 la quiz-ul de educație financiară! 🧠💸 Tu cât știi? https://finmate.hackathon.aico.dev`;
                return `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`;
            }
        }
    }
    </script>
<script src="https://unpkg.com/alpinejs" defer></script>
</html>
