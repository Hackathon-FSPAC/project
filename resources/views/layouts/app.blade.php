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
            questions: [],
            loading: true,

            init() {
                this.loadQuestionsFromAI();
            },

            loadQuestionsFromAI() {
                fetch('/chatbot/talk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: `Generează 10 întrebări de educație financiară în limba română, în format JSON strict cu următoarea structură:

{
  "questions": [
    {
      "text": "Întrebarea...",
      "options": ["Varianta A", "Varianta B", "Varianta C", "Varianta D"],
      "correct": 0
    }
  ]
}

Răspunsurile trebuie să fie clare, educative, relevante pentru tineri (16-25 ani), și să acopere teme precum: buget, economisire, credite, carduri, scor de credit, investiții, dobândă, salarii, cheltuieli. Nu include explicații.`
                    })
                })
                .then(res => res.json())
                .then(data => {
                try {
                    // Extrage doar partea dintre { și } pentru a forța parsarea
                    const jsonString = data.reply.match(/{[\s\S]*}/)?.[0];

                    if (!jsonString) {
                        throw new Error("JSON nu a fost găsit în răspunsul Gemini.");
                    }

                    const parsed = JSON.parse(jsonString);
                    this.questions = parsed.questions;
                    this.loading = false;
                } catch (err) {
                    console.error("❌ Eroare la parsare JSON:", err);
                    alert('Nu am putut încărca întrebările generate de AI.');
                }
            })
                .catch(err => {
                    console.error("❌ Eroare rețea:", err);
                    alert('Eroare la conectarea cu AI-ul.');
                });
            },

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
                    body: JSON.stringify({ score: this.score })
                })
                .then(res => res.json())
                .then(() => {
                    alert('✅ Scorul tău a fost partajat pe feed!');
                    window.location.href = '/dashboard'; // redirect automat
                })
                .catch(err => {
                    console.error(err);
                    alert('Eroare la partajare.');
                });
            }
        }
    }
</script>

<script src="https://unpkg.com/alpinejs" defer></script>
</html>
