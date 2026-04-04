<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">
                    <div class="mb-6">
                        <p class="text-lg">Welcome, <strong>{{ Auth::user()->name }}</strong></p>
                        <p class="text-sm text-gray-600 ">Role: <span class="font-semibold capitalize">{{ Auth::user()->role }}</span></p>
                        <p class="text-sm text-gray-600 ">Status: <span class="font-semibold capitalize">{{ Auth::user()->status }}</span></p>
                    </div>
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
