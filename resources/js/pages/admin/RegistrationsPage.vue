<template>
  <div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">إدارة طلبات التسجيل</h1>
        <p class="text-sm text-slate-500 mt-1">عرض وتصفية وتعديل جميع الطلبات الواردة.</p>
      </div>
      <button @click="handleExport" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition shadow-sm cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 17h16"/></svg>
        تصدير CSV
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <div class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
          <div class="relative">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input type="text" placeholder="البحث بالاسم أو الهاتف..." v-model="searchQuery" class="w-full pr-9 pl-3 py-2 text-sm rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-100 outline-none transition" />
          </div>
        </div>
        <select v-model="statusFilter" class="min-w-[140px] px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-100 outline-none transition cursor-pointer">
          <option value="all">كل الحالات</option>
          <option value="new">جديد</option>
          <option value="reviewing">قيد المراجعة</option>
          <option value="accepted">مقبول</option>
          <option value="rejected">مرفوض</option>
        </select>
        <select v-model="engineerFilter" class="min-w-[140px] px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-100 outline-none transition cursor-pointer">
          <option value="all">الكل (نقابة)</option>
          <option value="yes">منتمي للنقابة</option>
          <option value="no">غير منتمي</option>
        </select>
        <select v-model="employeeFilter" class="min-w-[140px] px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-100 outline-none transition cursor-pointer">
          <option value="all">الكل (موظفين)</option>
          <option value="yes">موظف</option>
          <option value="no">غير موظف</option>
        </select>
        <select v-model="studyLevelFilter" class="min-w-[140px] px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-100 outline-none transition cursor-pointer">
          <option value="all">كل المستويات</option>
          <option value="طالب اعدادي">طالب اعدادي</option>
          <option value="دبلوم">دبلوم</option>
          <option value="بكلوريوس">بكلوريوس</option>
          <option value="ماجستير">ماجستير</option>
          <option value="دكتوراه">دكتوراه</option>
          <option value="غير ذلك">غير ذلك</option>
        </select>
        <select v-model="trainingTypeFilter" class="min-w-[140px] px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white focus:border-slate-400 focus:ring-2 focus:ring-slate-100 outline-none transition cursor-pointer">
          <option value="all">كل أنواع التدريب</option>
          <option value="حضوري">حضوري</option>
          <option value="اون لاين">اون لاين</option>
        </select>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <!-- Toolbar -->
      <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50/50">
        <div class="text-sm font-semibold text-slate-700">
          إجمالي النتائج:
          <span class="mr-1 px-2 py-0.5 text-xs font-bold text-slate-800 bg-slate-200 rounded-full">{{ meta?.total || 0 }}</span>
        </div>
        <div v-if="loading" class="flex items-center gap-2 text-xs text-slate-400">
          <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
          جاري التحديث...
        </div>
      </div>

      <!-- Skeleton Loading -->
      <div v-if="loading && registrations.length === 0" class="p-6">
        <div class="space-y-3">
          <div v-for="i in 5" :key="i" class="flex gap-3 animate-pulse">
            <div class="h-10 bg-slate-100 rounded flex-1"></div>
            <div class="h-10 bg-slate-100 rounded w-24"></div>
            <div class="h-10 bg-slate-100 rounded w-32"></div>
            <div class="h-10 bg-slate-100 rounded w-40"></div>
            <div class="h-10 bg-slate-100 rounded w-28"></div>
            <div class="h-10 bg-slate-100 rounded w-24"></div>
            <div class="h-10 bg-slate-100 rounded w-24"></div>
            <div class="h-10 bg-slate-100 rounded w-20"></div>
            <div class="h-10 bg-slate-100 rounded w-20"></div>
            <div class="h-10 bg-slate-100 rounded w-24"></div>
            <div class="h-10 bg-slate-100 rounded w-24"></div>
          </div>
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-right text-sm border-collapse">
          <thead>
            <tr class="border-b border-slate-100">
              <th v-for="h in ['ت','رقم الطلب','الاسم','معلومات الاتصال','العنوان','الدراسة','التدريب','المهنة','النقابة','الحالة','التاريخ']" :key="h" class="px-5 py-3.5 font-bold text-xs text-slate-400 whitespace-nowrap tracking-wide">
                {{ h }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(reg, idx) in registrations" :key="reg.id" @click="openModal(reg)" class="group border-b border-slate-50 cursor-pointer transition hover:bg-slate-50">
              <td class="px-5 py-4 font-extrabold text-slate-700 text-xs">{{ meta ? (meta.current_page - 1) * meta.per_page + idx + 1 : idx + 1 }}</td>
              <td class="px-5 py-4 font-mono text-xs text-slate-400">{{ reg.order_id }}</td>
              <td class="px-5 py-4 font-bold text-slate-800">{{ reg.full_name }}</td>
              <td class="px-5 py-4">
                <div dir="ltr" class="text-left font-semibold text-slate-700 text-xs">{{ reg.phone }}</div>
                <div v-if="reg.email" dir="ltr" class="text-left text-[11px] text-slate-400 mt-0.5">{{ reg.email }}</div>
              </td>
              <td class="px-5 py-4 text-xs text-slate-400 max-w-[150px] truncate">{{ reg.address }}</td>
              <td class="px-5 py-4 text-xs font-semibold text-slate-600">{{ reg.study_level || "-" }}</td>
              <td class="px-5 py-4">
                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold" :class="reg.training_type === 'اون لاين' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-600'">{{ reg.training_type || "-" }}</span>
              </td>
              <td class="px-5 py-4 text-xs text-slate-500">{{ reg.is_employee || "-" }}</td>
              <td class="px-5 py-4">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold border" :class="reg.is_engineer ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-500 border-red-100'">{{ reg.is_engineer ? "نعم" : "لا" }}</span>
              </td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold border" :style="{ backgroundColor: getStatusStyle(reg.status).bg, color: getStatusStyle(reg.status).color, borderColor: getStatusStyle(reg.status).color + '30' }">
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: getStatusStyle(reg.status).dot }"></span>
                  {{ getStatusStyle(reg.status).label }}
                </span>
              </td>
              <td class="px-5 py-4 text-xs text-slate-400 whitespace-nowrap">{{ new Date(reg.created_at).toLocaleDateString('ar-IQ') }}</td>
            </tr>
            <tr v-if="registrations.length === 0 && !loading">
              <td colspan="11" class="px-5 py-16 text-center">
                <div class="flex flex-col items-center gap-3 text-slate-400">
                  <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                  <p class="text-sm font-medium">لا توجد نتائج مطابقة لبحثك</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="meta" class="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50/30">
        <span class="text-xs font-semibold text-slate-500">صفحة {{ meta.current_page }} من {{ meta.last_page }}</span>
        <div class="flex gap-2">
          <button @click="page = Math.max(1, page - 1)" :disabled="meta.current_page === 1" class="px-3 py-1.5 text-sm font-bold rounded-lg border transition" :class="meta.current_page === 1 ? 'bg-slate-100 text-slate-300 border-slate-200 cursor-not-allowed' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 cursor-pointer'">
            السابق
          </button>
          <button @click="page = Math.min(meta.last_page, page + 1)" :disabled="meta.current_page === meta.last_page" class="px-3 py-1.5 text-sm font-bold rounded-lg border transition" :class="meta.current_page === meta.last_page ? 'bg-slate-100 text-slate-300 border-slate-200 cursor-not-allowed' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 cursor-pointer'">
            التالي
          </button>
        </div>
      </div>
    </div>

    <RegistrationModal :open="!!selectedReg" :registration="selectedReg" :saving="saving" @close="closeModal" @save="onModalSave" @delete="onModalDelete" />
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import RegistrationModal from '@/components/RegistrationModal.vue';

const route = useRoute();

function debounce(fn, ms = 300) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), ms);
  };
}

const STATUS_STYLES = {
  new: { label: "جديد", color: "#1d4ed8", bg: "#eff6ff", dot: "#3b82f6" },
  reviewing: { label: "قيد المراجعة", color: "#b45309", bg: "#fffbeb", dot: "#f59e0b" },
  accepted: { label: "مقبول", color: "#047857", bg: "#ecfdf5", dot: "#10b981" },
  rejected: { label: "مرفوض", color: "#b91c1c", bg: "#fef2f2", dot: "#ef4444" },
};

const getStatusStyle = (status) => {
  return STATUS_STYLES[status] || STATUS_STYLES.new;
};

const registrations = ref([]);
const loading = ref(true);
const searchQuery = ref("");
const statusFilter = ref("all");
const engineerFilter = ref("all");
const employeeFilter = ref("all");
const studyLevelFilter = ref("all");
const trainingTypeFilter = ref("all");

const selectedReg = ref(null);
const saving = ref(false);

const page = ref(1);
const meta = ref(null);

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
  return match ? decodeURIComponent(match[3]) : null;
}

const fetchRegistrations = async () => {
  loading.value = true;
  try {
    const token = getCookie("admin_token");
    
    const params = new URLSearchParams();
    params.append("page", page.value.toString());
    if (searchQuery.value) params.append("search", searchQuery.value);
    if (statusFilter.value !== "all") params.append("status", statusFilter.value);
    if (engineerFilter.value !== "all") params.append("engineer", engineerFilter.value);
    if (employeeFilter.value !== "all") params.append("employee", employeeFilter.value);
    if (studyLevelFilter.value !== "all") params.append("study_level", studyLevelFilter.value);
    if (trainingTypeFilter.value !== "all") params.append("training_type", trainingTypeFilter.value);
    
    const res = await fetch(`/api/v1/admin/registrations?${params.toString()}`, {
      headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
      }
    });
    const data = await res.json();
    if (data.data) {
      registrations.value = data.data;
      meta.value = data.meta || null;
    }
  } catch (err) {
    // silently ignore
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchRegistrations();
});

const debouncedFetch = debounce(() => {
  fetchRegistrations();
}, 300);

watch([searchQuery, statusFilter, engineerFilter, employeeFilter, studyLevelFilter, trainingTypeFilter], () => {
  page.value = 1;
  debouncedFetch();
});

watch(page, () => {
  fetchRegistrations();
});

watch(() => route.query.id, (queryId) => {
  if (queryId && registrations.value.length > 0) {
    const found = registrations.value.find((r) => r.id === parseInt(queryId) || r.order_id === queryId);
    if (found) {
      openModal(found);
    }
  }
});

const openModal = (reg) => {
  selectedReg.value = reg;
};

const closeModal = () => {
  selectedReg.value = null;
};

const onModalSave = async ({ status, notes }) => {
  if (!selectedReg.value) return;
  saving.value = true;
  try {
    const token = getCookie("admin_token");
    const res = await fetch(`/api/v1/admin/registrations/${selectedReg.value.id}`, {
      method: "PUT",
      headers: {
        "Authorization": `Bearer ${token}`,
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({ status, admin_notes: notes })
    });
    if (res.ok) {
      closeModal();
      fetchRegistrations();
    } else {
      alert("فشل تحديث الطلب");
    }
  } catch (err) {
    alert("خطأ في الاتصال بالخادم");
  } finally {
    saving.value = false;
  }
};

const onModalDelete = async (id) => {
  if (!confirm("حذف هذا الطلب نهائياً؟")) return;
  try {
    const token = getCookie("admin_token");
    await fetch(`/api/v1/admin/registrations/${id}`, {
      method: "DELETE",
      headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
      }
    });
    closeModal();
    fetchRegistrations();
  } catch (err) {
    alert("خطأ في الحذف");
  }
};


const handleExport = async () => {
  try {
    const token = getCookie("admin_token");
    const params = new URLSearchParams();
    if (searchQuery.value) params.append("search", searchQuery.value);
    if (statusFilter.value !== "all") params.append("status", statusFilter.value);
    if (engineerFilter.value !== "all") params.append("engineer", engineerFilter.value);
    if (employeeFilter.value !== "all") params.append("employee", employeeFilter.value);
    if (studyLevelFilter.value !== "all") params.append("study_level", studyLevelFilter.value);
    if (trainingTypeFilter.value !== "all") params.append("training_type", trainingTypeFilter.value);

    const res = await fetch(`/api/v1/admin/registrations/export?${params.toString()}`, {
      headers: { "Authorization": `Bearer ${token}` }
    });
    const blob = await res.blob();
    const a = document.createElement("a");
    a.href = window.URL.createObjectURL(blob);
    a.download = `registrations_${new Date().getTime()}.csv`;
    document.body.appendChild(a);
    a.click();
    a.remove();
  } catch (err) {
    alert("حدث خطأ أثناء تصدير البيانات");
  }
};
</script>
