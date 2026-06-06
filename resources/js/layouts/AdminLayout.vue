<template>
  <div v-if="pathname.includes('/login')">
    <router-view />
  </div>

  <div v-else-if="!authenticated" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f7f9fb;">
    <svg class="animate-spin" style="width: 32px; height: 32px; color: #002a4d;" viewBox="0 0 24 24">
      <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
      <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
  </div>

  <div v-else style="direction: rtl; min-height: 100vh; background: #f7f9fb; font-family: 'IBM Plex Sans Arabic', 'Tajawal', sans-serif; color: #191c1e;">
    
    <!-- Mobile overlay -->
    <div v-if="sidebarOpen && !isDesktop"
      @click="sidebarOpen = false"
      style="position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; backdrop-filter: blur(4px);">
    </div>

    <!-- Sidebar -->
    <aside
      style="position: fixed; top: 0; bottom: 0; width: 220px; background: #ffffff; border-left: 1px solid #e0e3e5; z-index: 50; display: flex; flex-direction: column; transition: right 0.3s ease; overflow-y: auto;"
      :style="{ right: sidebarVisible ? '0' : '-220px' }"
    >
      <!-- Logo -->
      <div style="display: flex; align-items: center; gap: 10px; padding: 16px; border-bottom: 1px solid #e0e3e5;">
        <div style="position: relative; width: 36px; height: 36px; flex-shrink: 0;">
          <img src="/images/logo.png" alt="Logo" style="object-fit: contain; width: 100%; height: 100%;" />
        </div>
        <div>
          <div style="font-weight: 700; color: #002a4d; font-size: 14px;">بوابة التسجيل</div>
          <div style="font-size: 11px; color: #727780;">نظام الإدارة</div>
        </div>
      </div>

      <!-- Nav -->
      <nav style="flex: 1; padding: 12px 10px; display: flex; flex-direction: column; gap: 2px;">
        <router-link
          v-for="item in navItems"
          :key="item.href"
          :to="item.href"
          @click="sidebarOpen = false"
          class="nav-link"
          :class="{ active: pathname === item.href }"
        >
          <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
          </svg>
          <span>{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- Bottom -->
      <div style="padding: 12px 10px; border-top: 1px solid #e0e3e5; display: flex; flex-direction: column; gap: 6px;">
        <router-link
          to="/x-ctrl-7d3k/registrations"
          style="display: flex; align-items: center; justify-content: center; gap: 6px; background: #002a4d; color: #fff; font-weight: 600; font-size: 13px; padding: 10px; border-radius: 8px; text-decoration: none;"
        >
          <span>+</span> طلب جديد
        </router-link>
        <button
          @click="handleLogout"
          style="display: flex; align-items: center; gap: 8px; width: 100%; padding: 10px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #ba1a1a; background: transparent; border: none; cursor: pointer; font-family: inherit;"
        >
          <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
          </svg>
          تسجيل الخروج
        </button>
      </div>
    </aside>

    <!-- Main content area -->
    <div
      style="min-height: 100vh; display: flex; flex-direction: column; transition: margin-right 0.3s ease;"
      :style="{ marginRight: isDesktop ? '220px' : '0' }"
    >
      <!-- Header -->
      <header
        style="position: sticky; top: 0; z-index: 30; background: rgba(247,249,251,0.85); backdrop-filter: blur(12px); border-bottom: 1px solid #e0e3e5; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; height: 56px; flex-shrink: 0;"
      >
        <div style="display: flex; align-items: center; gap: 12px;">
          <!-- Mobile hamburger -->
          <button
            v-if="!isDesktop"
            @click="sidebarOpen = true"
            style="padding: 6px; border: none; background: transparent; cursor: pointer; color: #002a4d;"
          >
            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
          </button>
          <h1 style="color: #002a4d; font-weight: 700; font-size: 16px; margin: 0;">{{ pageTitle }}</h1>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <div style="position: relative; width: 32px; height: 32px; border-radius: 50%; overflow: hidden; border: 2px solid #e0e3e5;">
            <img src="/images/logo.png" alt="Admin" style="object-fit: cover; width: 100%; height: 100%;" />
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main style="flex: 1; padding: 20px; overflow-y: auto;">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const pathname = computed(() => route.path);

const sidebarOpen = ref(false);
const authenticated = ref(false);
const isDesktop = ref(false);

const navItems = [
  { href: "/x-ctrl-7d3k", label: "لوحة التحكم", icon: "M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" },
  { href: "/x-ctrl-7d3k/registrations", label: "طلبات التسجيل", icon: "M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" },
  { href: "/x-ctrl-7d3k/reports", label: "التقارير", icon: "M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" },
  { href: "/x-ctrl-7d3k/settings", label: "الإعدادات", icon: "M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z M15 12a3 3 0 11-6 0 3 3 0 016 0z" },
];

let mqHandler;

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
  return match ? decodeURIComponent(match[3]) : null;
}

const checkAuth = () => {
  const token = getCookie("admin_token");
  if (!token && !pathname.value.includes("/login")) {
    router.push("/x-ctrl-7d3k/login");
  } else {
    authenticated.value = true;
  }
};

watch(pathname, () => {
  checkAuth();
});

onMounted(() => {
  checkAuth();
  
  const mq = window.matchMedia("(min-width: 1024px)");
  isDesktop.value = mq.matches;
  
  mqHandler = (e) => { isDesktop.value = e.matches; };
  mq.addEventListener("change", mqHandler);
});

onUnmounted(() => {
  if (mqHandler) {
    window.matchMedia("(min-width: 1024px)").removeEventListener("change", mqHandler);
  }
});

const handleLogout = () => {
  document.cookie = "admin_token=; path=/; max-age=0; SameSite=Strict";
  router.push("/x-ctrl-7d3k/login");
};

const pageTitle = computed(() => {
  if (pathname.value === "/x-ctrl-7d3k") return "الإحصائيات العامة";
  if (pathname.value.includes("registrations")) return "إدارة الطلبات";
  if (pathname.value.includes("reports")) return "التقارير";
  if (pathname.value.includes("settings")) return "الإعدادات";
  return "بوابة الإدارة";
});

const sidebarVisible = computed(() => isDesktop.value || sidebarOpen.value);
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.15s;
  background: transparent;
  color: #42474f;
}

.nav-link.active {
  background: #002a4d;
  color: #fff;
}

.nav-link.active svg {
  color: #fff !important;
}

.nav-link svg {
  color: #727780;
}
</style>
