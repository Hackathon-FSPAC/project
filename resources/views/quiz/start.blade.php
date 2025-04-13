@extends('layouts.app')

@section('content')
<div x-data="quizApp()" x-init="init()" class="max-w-2xl mx-auto py-10 px-4">

    {{-- Întrebări --}}
    <template x-if="currentQuestion < questions.length">
        <div class="bg-white shadow rounded-xl p-6">
            <h2 class="text-xl font-bold mb-4" x-text="questions[currentQuestion].text"></h2>

            <ul class="space-y-3">
                <template x-for="(option, index) in questions[currentQuestion].options" :key="index">
                    <li>
                        <button
                            class="w-full text-left px-4 py-2 rounded border transition"
                            :class="{
                                'bg-green-100 border-green-400': showAnswer && index === questions[currentQuestion].correct,
                                'bg-red-100 border-red-400': showAnswer && selectedAnswer === index && index !== questions[currentQuestion].correct,
                                'hover:bg-gray-100 border-gray-300': !showAnswer
                            }"
                            :disabled="showAnswer"
                            @click="selectAnswer(index)"
                            x-text="option">
                        </button>
                    </li>
                </template>
            </ul>

            <div class="mt-4 text-right" x-show="showAnswer">
                <button @click="nextQuestion()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Următoarea întrebare
                </button>
            </div>
        </div>
    </template>

    {{-- Rezultat final --}}
    <template x-if="currentQuestion >= questions.length">
        <div class="bg-white shadow rounded-xl p-6 text-center">
            <h2 class="text-2xl font-bold">✅ Scor final: <span x-text="score"></span>/10</h2>
            <p class="mt-2 text-gray-600 text-lg" x-text="getFeedback()"></p>

            <button
                @click="shareToFeed()"
                class="mt-6 bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                📤 Partajează scorul pe feed
            </button>
            <button
                @click="window.location.href = '/dashboard'"
                class="mt-4 bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600 transition">
                ⬅️ Înapoi la feed
            </button>

            {{-- Feedback AI --}}
            <div x-transition.opacity x-show="feedback" class="mt-6 bg-yellow-50 border border-yellow-300 rounded-xl p-5 text-left shadow">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">📚 Recomandări personalizate</h3>
                <p class="text-sm text-gray-800 whitespace-pre-line" x-text="feedback"></p>
            </div>
        </div>
    </template>
</div>

{{-- SCRIPT --}}
<script>
function quizApp() {
    return {
        currentQuestion: 0,
        selectedAnswer: null,
        score: 0,
        showAnswer: false,
        userAnswers: [],
        feedback: '',
        questions: [],

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
                    message: `Generează 10 întrebări de educație financiară în limba română, format JSON strict:
{
  "questions": [
    {
      "text": "Întrebarea...",
      "options": ["A", "B", "C", "D"],
      "correct": 0
    }
  ]
}`
                })
            })
            .then(res => res.json())
            .then(data => {
                const jsonStr = data.reply.match(/{[\s\S]*}/)?.[0];
                if (!jsonStr) throw new Error("Răspuns invalid de la AI");
                this.questions = JSON.parse(jsonStr).questions;
            })
            .catch(err => {
                console.error(err);
                alert('❌ Eroare la încărcarea întrebărilor.');
            });
        },

        selectAnswer(index) {
            this.selectedAnswer = index;
            this.userAnswers[this.currentQuestion] = index;
            this.showAnswer = true;
            if (index === this.questions[this.currentQuestion].correct) {
                this.score++;
            }
        },

        nextQuestion() {
            this.currentQuestion++;
            if (this.currentQuestion === this.questions.length) {
                this.generatePersonalizedFeedback();
            }
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ score: this.score })
            })
            .then(res => res.json())
            .then(() => alert('✅ Scorul tău a fost partajat pe feed!'))
            .catch(() => alert('❌ Eroare la partajare.'));
        },

        generatePersonalizedFeedback() {
            const incorrectQuestions = this.questions
                .map((q, i) => ({
                    question: q.text,
                    selected: this.userAnswers?.[i] ?? null,
                    correct: q.correct,
                    options: q.options
                }))
                .filter((q, i) => this.userAnswers?.[i] !== q.correct);

                const prompt = `Am terminat un quiz de educație financiară și am greșit următoarele întrebări:\n\n${incorrectQuestions.map(q =>
                    `Întrebare: ${q.question}\nRăspuns ales: ${q.options[q.selected] ?? 'Nespecificat'}\nRăspuns corect: ${q.options[q.correct]}\n`
                ).join('\n')}\n\nPe baza acestor greșeli, oferă-mi recomandări despre ce concepte financiare ar trebui să învăț mai bine. Nu folosi Markdown sau caractere pentru bold (**). Răspuns simplu, cu liniuțe sau paragrafe normale.`;

            fetch('/chatbot/analyze-quiz', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: prompt })
            })
            .then(res => res.json())
            .then(data => {
                this.feedback = data.reply;
            })
            .catch(() => {
                this.feedback = "❌ Nu s-au putut genera sugestii. Încearcă mai târziu.";
            });
        }
    };
}
</script>
@endsection
