@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to products
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <p class="text-gray-600 mb-4">{{ $product->description }}</p>
                    <span class="text-xl font-semibold text-indigo-600">${{ number_format($product->price, 2) }}</span>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('products.edit', $product) }}" class="text-yellow-600 hover:text-yellow-800 p-2 rounded-full hover:bg-yellow-50">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-full hover:bg-red-50" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Assistant Section -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-robot text-indigo-600 mr-2"></i> Product Assistant
            </h2>
            <p class="text-sm text-gray-500 mt-1">Ask me anything about this product</p>
        </div>
        
        <div class="p-6">
            <!-- Chat Container -->
            <div id="chat-container" class="h-80 overflow-y-auto mb-4 space-y-4 pr-2">
                <!-- Welcome message -->
                <div class="flex justify-start">
                    <div class="bg-gray-100 rounded-lg py-2 px-4 max-w-xs lg:max-w-md">
                        <p class="text-sm text-gray-800">Hello! I'm your product assistant. Ask me anything about <strong>{{ $product->name }}</strong>.</p>
                    </div>
                </div>
                <!-- Messages will appear here dynamically -->
            </div>
            
            <!-- Input Form -->
            <form id="question-form" class="flex items-center">
                @csrf
                <input 
                    type="text" 
                    id="question-input" 
                    placeholder="Type your question here..." 
                    class="flex-1 border border-gray-300 rounded-l-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    autocomplete="off"
                >
                <button 
                    type="submit" 
                    class="bg-indigo-600 text-white py-2 px-4 rounded-r-lg hover:bg-indigo-700 transition flex items-center justify-center"
                >
                    <span class="mr-2">Send</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('question-form');
    const input = document.getElementById('question-input');
    const chatContainer = document.getElementById('chat-container');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const question = input.value.trim();
        if (!question) return;
        
        // Add user question to chat
        addMessageToChat('user', question);
        input.value = '';
        
        // Show loading indicator
        const loadingId = 'loading-' + Date.now();
        addLoadingIndicator(loadingId);
        
        // Send to server
        fetch(`/products/{{ $product->id }}/ask`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ question: question })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            removeLoadingIndicator(loadingId);
            addMessageToChat('assistant', data.answer);
        })
        .catch(error => {
            removeLoadingIndicator(loadingId);
            addMessageToChat('assistant', 'Sorry, there was an error processing your question. Please try again.');
            console.error('Error:', error);
        });
    });
    
    function addMessageToChat(sender, message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = `rounded-lg py-2 px-4 max-w-xs lg:max-w-md ${
            sender === 'user' 
                ? 'bg-indigo-600 text-white' 
                : 'bg-gray-100 text-gray-800'
        }`;
        bubbleDiv.innerHTML = `<p class="text-sm">${message}</p>`;
        
        messageDiv.appendChild(bubbleDiv);
        chatContainer.appendChild(messageDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function addLoadingIndicator(id) {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = id;
        loadingDiv.className = 'flex justify-start';
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'bg-gray-100 rounded-lg py-2 px-4 max-w-xs lg:max-w-md';
        bubbleDiv.innerHTML = `
            <div class="flex space-x-2">
                <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce"></div>
                <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.4s"></div>
            </div>
        `;
        
        loadingDiv.appendChild(bubbleDiv);
        chatContainer.appendChild(loadingDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function removeLoadingIndicator(id) {
        const element = document.getElementById(id);
        if (element) {
            element.remove();
        }
    }
    
    // Focus input on load
    input.focus();
});
</script>
@endsection