<x-filament-panels::page>
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($this->getStats() as $stat)
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex items-center gap-x-4">
                @if ($stat->getIcon())
                    <div class="rounded-lg bg-teal-50 p-2 text-teal-600 dark:bg-teal-950/50 dark:text-teal-400">
                        <!-- Render the icon manually using Blade or inline SVG depending on configuration -->
                        <span class="h-6 w-6 block">
                            @svg($stat->getIcon(), 'h-6 w-6')
                        </span>
                    </div>
                @endif
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $stat->getLabel() }}
                    </span>
                    <h3 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        {{ $stat->getValue() }}
                    </h3>
                    @if ($stat->getDescription())
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $stat->getDescription() }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Applications & Upcoming Interviews Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <!-- Recent Applications -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="text-lg font-bold text-gray-950 dark:text-white mb-4">Recent Applications</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-2">App Number</th>
                            <th class="px-4 py-2">Candidate</th>
                            <th class="px-4 py-2">Job</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Applied</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($this->getRecentApplications() as $app)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $app['application_number'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                                    {{ $app['candidate_name'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $app['job_title'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset" 
                                          style="
                                              @switch($app['status'])
                                                  @case('new') background-color: rgba(107, 114, 128, 0.1); color: #6b7280; ring-color: rgba(107, 114, 128, 0.2); @break
                                                  @case('screening') background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; ring-color: rgba(59, 130, 246, 0.2); @break
                                                  @case('shortlisted') background-color: rgba(99, 102, 241, 0.1); color: #6366f1; ring-color: rgba(99, 102, 241, 0.2); @break
                                                  @case('hired') background-color: rgba(34, 197, 94, 0.1); color: #22c55e; ring-color: rgba(34, 197, 94, 0.2); @break
                                                  @case('rejected') background-color: rgba(239, 68, 68, 0.1); color: #ef4444; ring-color: rgba(239, 68, 68, 0.2); @break
                                                  @default background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; ring-color: rgba(245, 158, 11, 0.2);
                                              @endswitch
                                          ">
                                        {{ $app['status_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $app['applied_at'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-400">No recent applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Interviews -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h2 class="text-lg font-bold text-gray-950 dark:text-white mb-4">Upcoming Interviews</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-2">Candidate</th>
                            <th class="px-4 py-2">Job</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Date / Time</th>
                            <th class="px-4 py-2">Mode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($this->getUpcomingInterviews() as $interview)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $interview['candidate_name'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                                    {{ $interview['job_title'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $interview['type'] }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $interview['date'] }}</div>
                                    <div class="text-xs text-gray-400">{{ $interview['time'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-400">{{ $interview['mode'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-400">No upcoming interviews scheduled.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
