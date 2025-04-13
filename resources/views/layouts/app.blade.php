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
    @auth
        <header class="w-full flex justify-between items-center px-6 py-4 bg-white/70 backdrop-blur-md shadow-sm fixed top-0 z-50 border-b border-gray-100/30">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center shadow-inner">
                    <span class="text-xl">👋</span>
                </div>
                <h1 class="text-lg font-semibold text-gray-800">Bun venit, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600">{{ Auth::user()->name ?? 'Utilizator' }}</span></h1>
            </div>

            <div class="relative group">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 bg-gradient-to-r from-pink-500 to-red-500 text-white px-5 py-2.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.03] active:scale-95 group-hover:from-pink-600 group-hover:to-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:animate-pulse" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-medium">Logout</span>
                        <span class="absolute -right-1 -top-1 h-3 w-3">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 group-hover:opacity-100"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                    </button>
                </form>
                <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap">Sesiune curentă: {{ Auth::user()->email }}</div>
                    <div class="w-3 h-3 bg-gray-800 rotate-45 absolute -top-1.5 left-1/2 -translate-x-1/2"></div>
                </div>
            </div>
        </header>

        <div class="pt-24"></div> <!-- spațiu de compensare pentru header-ul fix -->
    @endauth
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
            userAnswers: [],
            feedback: null,

            init() {
                this.loadQuestionsFromAI();
            },

            loadQuestionsFromAI() {
                fetch('/quiz/generate', {
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
                this.userAnswers.push(index);
                if (index === this.questions[this.currentQuestion].correct) {
                    this.score++;
                }
            },

            nextQuestion() {
                this.currentQuestion++;
                this.selectedAnswer = null;
                this.showAnswer = false;
                if (this.currentQuestion === this.questions.length - 1) {
                this.getAnalysis(); // chemăm AI-ul!
                }   
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
                    body: JSON.stringify({ score: parseInt(this.score) })
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
            getAnalysis() {
                fetch('/quiz/analyze', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        score: this.score,
                        answers: this.questions.map((q, index) => ({
                            text: q.text,
                            options: q.options,
                            correct: q.correct,
                            selected: index < this.currentQuestion ? this.userAnswers[index] : null
                        }))
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.feedback = data.feedback;
                })
                .catch(err => {
                    console.error(err);
                    this.feedback = 'Eroare la generarea feedback-ului.';
                });
}

        }
    }
</script>

<script src="https://unpkg.com/alpinejs" defer></script>
</html>
