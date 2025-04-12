@extends('layouts.app')

@section('content')
<div x-data="quizApp()" class="max-w-2xl mx-auto py-10 px-4">

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
                // adaugă restul întrebărilor aici
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                    },
                    body: JSON.stringify({ score: this.score })
                })
                .then(res => res.json())
                .then(data => {
                    alert('✅ Scorul tău a fost partajat pe feed!');
                })
                .catch(err => {
                    console.error(err);
                    alert('❌ Eroare la partajare.');
                });
            }
        }
    }
</script>
@endsection
