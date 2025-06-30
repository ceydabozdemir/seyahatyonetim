<div class="flex justify-start space-x-3 mt-8">
    {{-- Download Report Button --}}
    <a
        href="{{ route('expenses.download-report') }}"
        target="_blank"
        class="inline-flex items-center h-8 px-4 bg-gray-200 dark:bg-gray-700 text-orange-600 dark:text-orange-400 text-sm font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200"
    >
        <x-heroicon-o-document-arrow-down class="w-4 h-4 mr-2 text-orange-600 dark:text-orange-400" />
        Raporu İndir
    </a>

    {{-- Download Charts Button --}}
    <a
        href="{{ route('expenses.download-charts') }}"
        target="_blank"
        class="inline-flex items-center h-8 px-4 bg-gray-200 dark:bg-gray-700 text-orange-600 dark:text-orange-400 text-sm font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200"
    >
        <x-heroicon-o-chart-bar class="w-4 h-4 mr-2 text-orange-600 dark:text-orange-400" />
        Grafikleri İndir
    </a>
</div>
