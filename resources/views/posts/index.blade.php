<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Blog') }}
            </h2>

            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('New post') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @forelse ($posts as $post)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('By :author on :date', ['author' => $post->author->name, 'date' => $post->created_at->format('d/m/Y')]) }}
                    </p>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-500">
                    {{ __('No posts yet.') }}
                </div>
            @endforelse

            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
