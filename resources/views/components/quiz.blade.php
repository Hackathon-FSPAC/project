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

    <div x-show="currentQuestion >= questions.length" class="text-center mt-6">
        <h2 class="text-2xl font-bold">✅ Scor final: <span x-text="score"></span>/10</h2>
        <p class="mt-2 text-gray-600" x-text="getFeedback()"></p>
    
        <button
            @click="shareToFeed()"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            📤 Partajează scorul pe feed
        </button>
    </div>
    

    <div class="text-right">
        <button
            x-show="showAnswer && currentQuestion < questions.length"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded"
            @click="nextQuestion()">
            Următoarea întrebare
        </button>
    </div>
</div>
