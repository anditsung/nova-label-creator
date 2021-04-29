<nova-sidebar>
    <template>
        <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="var(--sidebar-icon)" d="M2.59 13.41A1.98 1.98 0 0 1 2 12V7a5 5 0 0 1 5-5h4.99c.53 0 1.04.2 1.42.59l8 8a2 2 0 0 1 0 2.82l-8 8a2 2 0 0 1-2.82 0l-8-8zM20 12l-8-8H7a3 3 0 0 0-3 3v5l8 8 8-8zM7 8a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg>
        <span class="sidebar-label">
            {{ __("Label Creator") }}
        </span>
    </template>

    <template v-slot:menu>

        <li class="leading-wide mb-4 text-sm">
            <router-link
                :to="{
                    name: 'labels'
                }" class="text-white ml-8 no-underline dim">
                {{ __("Labels") }}
            </router-link>
        </li>

        <li class="leading-wide mb-4 text-sm">
            <router-link
                :to="{
                name: 'index',
                params: {
                    resourceName: 'label-types',
                }
            }" class="text-white ml-8 no-underline dim">
                {{ __("Label Types") }}
            </router-link>
        </li>
    </template>

</nova-sidebar>
