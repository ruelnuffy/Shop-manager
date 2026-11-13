<x-admin-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                    Notifications
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Staff logins, POS sales, and storefront order alerts.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <button
                    type="button"
                    onclick="if (window.requestStoreNotificationPermission) window.requestStoreNotificationPermission()"
                    id="enablePushAlertsBtn"
                    class="hidden px-4 py-2 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition cursor-pointer"
                >
                    Enable Browser / Mobile Alerts
                </button>

                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                        @csrf
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition cursor-pointer"
                        >
                            Mark All Read ({{ $unreadCount }})
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden divide-y divide-slate-100">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $eventType = $data['event_type'] ?? 'info';
                    $isUnread = $notification->read_at === null;
                    $badgeClasses = match ($eventType) {
                        'staff_login' => 'bg-indigo-50 text-indigo-700',
                        'staff_sale' => 'bg-emerald-50 text-emerald-700',
                        'storefront_order' => 'bg-amber-50 text-amber-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                    $badgeLabel = match ($eventType) {
                        'staff_login' => 'Staff Login',
                        'staff_sale' => 'POS Sale',
                        'storefront_order' => 'Storefront Order',
                        default => 'Alert',
                    };
                @endphp
                <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isUnread ? 'bg-blue-50/30' : 'bg-white' }}">
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if ($isUnread)
                                <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                            @endif
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $badgeClasses }}">
                                {{ $badgeLabel }}
                            </span>
                            <span class="text-xs text-slate-400">
                                {{ $notification->created_at?->diffForHumans() }}
                            </span>
                        </div>

                        <h2 class="font-bold text-slate-900 text-sm sm:text-base">
                            {{ $data['title'] ?? 'Notification' }}
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {{ $data['message'] ?? '' }}
                        </p>

                        @if (! empty($data['meta']['summary']))
                            <p class="text-[11px] text-slate-400">
                                {{ $data['meta']['summary'] }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition cursor-pointer"
                            >
                                {{ $data['action_label'] ?? 'View' }} &rarr;
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center space-y-2">
                    <div class="text-2xl">🔔</div>
                    <h3 class="font-bold text-slate-900 text-base">No notifications yet</h3>
                    <p class="text-xs text-slate-500">
                        You will see alerts here when staff log in, record POS sales, or customers place storefront orders.
                    </p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="pt-2">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('enablePushAlertsBtn');
            if (btn && 'Notification' in window && Notification.permission === 'default') {
                btn.classList.remove('hidden');
            }
        });
    </script>
</x-admin-layout>
