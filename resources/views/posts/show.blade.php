<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500">
                    {{ __('By :author on :date', ['author' => $post->author->name, 'date' => $post->created_at->format('d/m/Y')]) }}
                </p>

                <div class="mt-4 text-gray-900 whitespace-pre-line">{{ $post->content }}</div>

                @can('update', $post)
                    <div class="mt-6 flex gap-4">
                        <a href="{{ route('posts.edit', $post) }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Edit') }}</a>

                        @can('delete', $post)
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('{{ __('Delete this post?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                            </form>
                        @endcan
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
