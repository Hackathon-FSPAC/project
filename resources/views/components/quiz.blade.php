<div x-data="quizApp()" class="bg-white p-6 rounded-xl shadow space-y-4">
    <template x-if="currentQuestion < questions.length">
        <div>
            <p class="font-semibold text-lg mb-2" x-text="'Întrebarea ' + (currentQuestion + 1) + ': ' + questions[currentQuestion].text"></p>
            <template x-for="(option, index) in questions[currentQuestion].options" :key="index">
                <button
                    class="block w-full text-left px-4 py-2 my-1 rounded border border-gray-300 hover:bg-gray-100"
                    :class="{
                        'bg-green-100': showAnswer && index === questions[currentQuestion].correct,
                        'bg-red-100': showAnswer && index === selectedAnswer && index !== questions[currentQuestion].correct
                    }"
                    @click="selectAnswer(index)"
                    x-text="option">
                </button>
            </template>
        </div>
    </template>

    <template x-if="currentQuestion >= questions.length">
        <div class="text-center space-y-4">
            <h3 class="text-xl font-bold">🎉 Ai terminat quiz-ul!</h3>
            <p class="mt-2 text-lg">Scorul tău: <span x-text="score"></span>/10</p>
            <p class="mt-2 font-semibold" x-text="getFeedback()"></p>
            <a :href="generateShareLink()" class="inline-block underline text-blue-600" target="_blank">
                📤 Partajează-ți scorul
            </a>
    
            <div class="flex justify-center gap-4 mt-6">
                <!-- Buton refă testul -->
                <button @click="restartQuiz()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    🔁 Refă testul
                </button>
    
                <!-- Buton înapoi la dashboard -->
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    🏠 Înapoi la dashboard
                </a>
            </div>
        </div>
    </template>
    

    <div class="text-right">
        <button
            x-show="showAnswer && currentQuestion < questions.length"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded"
            @click="nextQuestion()">
            Următoarea întrebare
        </button>
    </div>
</div>
