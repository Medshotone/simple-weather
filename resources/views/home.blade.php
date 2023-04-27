<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{ __("You're logged in!") }}
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <form id="token-form" method="GET" action="{{ route('token') }}">
                        @csrf

                        <x-button class="ml-3">
                            {{ __('Generate token') }}
                        </x-button>
                    </form>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <pre>
                        @dump($userData)
                    </pre>
                    <pre>
                        @dump($weatherData)
                    </pre>
                </div>
            </div>
        </div>
    </div>
    <script>
        const form = document.querySelector('#token-form');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            fetch(form.action, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                }
            })
                .then(response => response.json())
                .then(json => {
                    let button = form.querySelector('button[type="submit"]');
                    button.style.textTransform = 'none';
                    button.textContent = json.data.token;
                });
        });
    </script>
</x-app-layout>
