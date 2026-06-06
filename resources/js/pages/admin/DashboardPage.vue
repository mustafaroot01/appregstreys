<template>
  <div class="flex flex-col gap-6">
    <!-- Skeleton Loading -->
    <div v-if="loading || !statsData" class="space-y-6">
      <div class="h-24 bg-slate-200 rounded-xl animate-pulse"></div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="h-32 bg-slate-200 rounded-xl animate-pulse"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="h-64 bg-slate-200 rounded-xl animate-pulse"></div>
        <div class="h-64 bg-slate-200 rounded-xl animate-pulse"></div>
      </div>
      <div class="h-72 bg-slate-200 rounded-xl animate-pulse"></div>
    </div>

    <div v-else class="flex flex-col gap-6">
      <!-- Welcome Banner -->
      <div class="flex items-center justify-between flex-wrap gap-4 bg-gradient-to-bl from-slate-800 to-slate-900 px-6 py-5 rounded-xl text-white shadow-lg">
        <div>
          <h1 class="text-2xl font-extrabold tracking-tight">مرحباً بك في لوحة التحكم</h1>
          <p class="text-sm text-slate-300 mt-1">نظرة عامة على نشاط التسجيل وإحصائيات المتقدمين.</p>
        </div>
        <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-lg text-sm font-bold">
          <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          {{ currentDate }}
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="(s, i) in stats" :key="i" class="relative overflow-hidden rounded-xl p-5 flex flex-col gap-4 transition hover:-translate-y-0.5" :class="s.highlight ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-white border border-slate-200 shadow-sm'">
          <div class="flex items-center justify-between">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center text-lg" :class="s.highlight ? 'bg-white/20' : 'bg-slate-100'">{{ s.emoji }}</div>
            <span v-if="s.change" class="text-[11px] font-extrabold px-2.5 py-1 rounded-full" :class="s.highlight ? 'bg-white text-emerald-800' : 'bg-emerald-50 text-emerald-700'">{{ s.change }}</span>
          </div>
          <div>
            <div class="text-xs font-bold mb-1" :class="s.highlight ? 'text-emerald-100' : 'text-slate-400'">{{ s.label }}</div>
            <div class="text-3xl font-extrabold tracking-tight">{{ s.value }}</div>
          </div>
        </div>
      </div>

      <!-- Chart & Alerts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Bar Chart -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center justify-between mb-8">
            <h3 class="text-sm font-extrabold text-slate-800">نشاط التسجيل (7 أيام)</h3>
            <router-link to="/x-ctrl-7d3k/reports" class="text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition">
              عرض التفاصيل
            </router-link>
          </div>
          <div class="flex items-end gap-3 h-48">
            <div v-for="(bar, idx) in chartData" :key="idx" class="flex-1 flex flex-col items-center h-full justify-end">
              <div class="text-[11px] font-extrabold mb-1.5" :class="bar.isToday ? 'text-emerald-600' : 'text-slate-400'">{{ bar.val }}</div>
              <div class="w-full max-w-[36px] rounded-t-md transition-all duration-700 ease-out"
                :class="bar.isToday ? 'bg-gradient-to-t from-emerald-500 to-emerald-400' : 'bg-gradient-to-t from-slate-500 to-slate-400'"
                :style="{ height: `${Math.max((chartMax > 0 ? (bar.val / chartMax) * 100 : 0), 5)}%` }">
              </div>
              <div class="text-[10px] font-bold text-slate-400 mt-2 text-center">{{ bar.label }}</div>
            </div>
          </div>
        </div>

        <!-- Alerts -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
          <h3 class="text-sm font-extrabold text-slate-800 mb-4">تنبيهات النظام</h3>
          <div class="flex flex-col gap-3 flex-1">
            <div v-for="(a, i) in alerts" :key="i" class="flex items-center gap-3 p-3 rounded-lg border transition hover:scale-[1.01] cursor-default" :style="{ backgroundColor: a.bg, borderColor: a.border }">
              <div class="w-9 h-9 bg-white rounded-full flex items-center justify-center shadow-sm text-base shrink-0">{{ a.icon }}</div>
              <div>
                <div class="text-sm font-bold text-slate-800">{{ a.title }}</div>
                <div class="text-xs text-slate-500 font-medium mt-0.5">{{ a.desc }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
          <h3 class="text-sm font-extrabold text-slate-800">آخر الطلبات المستلمة</h3>
          <router-link to="/x-ctrl-7d3k/registrations" class="text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg transition shadow-sm">
            عرض الكل
          </router-link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-right text-sm border-collapse">
            <thead>
              <tr class="border-b border-slate-100">
                <th class="px-6 py-3.5 font-bold text-xs text-slate-400 whitespace-nowrap">الاسم الكامل</th>
                <th class="px-6 py-3.5 font-bold text-xs text-slate-400 whitespace-nowrap">رقم الهاتف</th>
                <th class="px-6 py-3.5 font-bold text-xs text-slate-400 whitespace-nowrap">الدراسة والمهنة</th>
                <th class="px-6 py-3.5 font-bold text-xs text-slate-400 whitespace-nowrap">التاريخ</th>
                <th class="px-6 py-3.5 font-bold text-xs text-slate-400 text-center">الحالة</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="reg in (statsData.recent_registrations || []).slice(0, 6)" :key="reg.id" class="group border-b border-slate-50 transition hover:bg-slate-50">
                <td class="px-6 py-4 font-bold text-slate-800">{{ reg.full_name }}</td>
                <td dir="ltr" class="px-6 py-4 text-left font-mono text-xs text-slate-500">{{ reg.phone }}</td>
                <td class="px-6 py-4">
                  <div class="flex gap-2 items-center">
                    <span v-if="reg.study_level" class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ reg.study_level }}</span>
                    <span v-if="reg.is_engineer" class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded border border-emerald-100">نقابة</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-xs text-slate-400 font-medium">{{ new Date(reg.created_at).toLocaleDateString('ar-IQ') }}</td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border" :style="{ backgroundColor: getStatusStyle(reg.status).bg, color: getStatusStyle(reg.status).color, borderColor: getStatusStyle(reg.status).color + '30' }">
                    <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: getStatusStyle(reg.status).dot }"></span>
                    {{ getStatusStyle(reg.status).label }}
                  </span>
                </td>
              </tr>
              <tr v-if="!statsData.recent_registrations || statsData.recent_registrations.length === 0">
                <td colspan="5" class="px-6 py-16 text-center">
                  <div class="flex flex-col items-center gap-3 text-slate-400">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <p class="text-sm font-medium">لا توجد طلبات حديثة</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const STATUS_STYLES = {
  new: { label: "جديد", color: "#1d4ed8", bg: "#eff6ff", dot: "#3b82f6" },
  reviewing: { label: "قيد المراجعة", color: "#b45309", bg: "#fffbeb", dot: "#f59e0b" },
  accepted: { label: "مقبول", color: "#047857", bg: "#ecfdf5", dot: "#10b981" },
  rejected: { label: "مرفوض", color: "#b91c1c", bg: "#fef2f2", dot: "#ef4444" },
};

const getStatusStyle = (status) => {
  return STATUS_STYLES[status] || STATUS_STYLES.new;
};

const loading = ref(true);
const statsData = ref(null);
const currentDate = ref("");

const alerts = [
  { icon: "⚠️", title: "مراجعة الطلبات المكتملة", desc: "يوجد طلبات بانتظار اتخاذ قرار بالقبول أو الرفض.", bg: "#fffbeb", border: "#fde68a" },
  { icon: "✅", title: "النسخ الاحتياطي يعمل", desc: "تم حفظ نسخة من البيانات بنجاح في السحابة.", bg: "#ecfdf5", border: "#a7f3d0" },
  { icon: "ℹ️", title: "تحديثات النظام", desc: "تم تطبيق فلاتر البحث المتقدمة بنجاح.", bg: "#eff6ff", border: "#bfdbfe" },
];

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
  return match ? decodeURIComponent(match[3]) : null;
}

onMounted(async () => {
  currentDate.value = new Date().toLocaleDateString("ar-IQ", { weekday: "long", year: "numeric", month: "long", day: "numeric" });
  
  try {
    const token = getCookie("admin_token");
    const res = await fetch("/api/v1/admin/dashboard", {
      headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
      }
    });
    const data = await res.json();
    if (data.success) {
      statsData.value = data.data;
    }
  } catch (err) {
    // silently ignore
  } finally {
    loading.value = false;
  }
});

const chartData = computed(() => {
  if (!statsData.value) return [];
  return (statsData.value.daily_stats || []).map((d, i) => ({
    label: d.label,
    val: d.count,
    isToday: i === 6
  }));
});

const chartMax = computed(() => {
  if (chartData.value.length === 0) return 10;
  return Math.max(...chartData.value.map(d => d.val), 10);
});

const stats = computed(() => {
  if (!statsData.value) return [];
  return [
    { label: "إجمالي المسجلين", value: statsData.value.total, emoji: "👥", change: "" },
    { label: "المنتمون للنقابة", value: statsData.value.engineers, emoji: "🛡️", change: "" },
    { label: "غير المنتمين", value: statsData.value.non_engineers, emoji: "🎓" },
    { label: "تسجيلات اليوم", value: statsData.value.today, emoji: "📅", highlight: true },
  ];
});
</script>
