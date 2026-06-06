<template>
  <div style="display: flex; flex-direction: column; gap: 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      <div>
        <h1 style="font-size: 22px; font-weight: 700; color: #002a4d; margin: 0;">التقارير والإحصائيات</h1>
        <p style="font-size: 14px; color: #727780; margin-top: 4px;">التحليلات الديموغرافية والجغرافية.</p>
      </div>
      <button @click="print" style="background: #002a4d; color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;">
        🖨️ طباعة
      </button>
    </div>

    <div v-if="loading" style="padding: 40px; text-align: center; color: #727780;">
      جاري التحميل...
    </div>

    <template v-else>
      <!-- Demographics -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        
        <!-- Gender -->
        <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e0e3e5;">
          <h3 style="font-size: 14px; font-weight: 700; color: #002a4d; margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 1px solid #e0e3e5;">التوزيع حسب الجنس</h3>
          <div style="display: flex; justify-content: space-around; padding: 16px 0; background: #f8f9fa; border-radius: 8px; margin-bottom: 16px;">
            <div style="text-align: center;">
              <div style="font-size: 28px;">👨</div>
              <div style="font-size: 12px; color: #727780; font-weight: 600; margin-top: 4px;">الذكور</div>
              <div style="font-size: 22px; font-weight: 700; color: #002a4d;">{{ maleCount }}</div>
            </div>
            <div style="width: 1px; background: #e0e3e5;"></div>
            <div style="text-align: center;">
              <div style="font-size: 28px;">👩</div>
              <div style="font-size: 12px; color: #727780; font-weight: 600; margin-top: 4px;">الإناث</div>
              <div style="font-size: 22px; font-weight: 700; color: #002a4d;">{{ femaleCount }}</div>
            </div>
          </div>
          
          <div v-for="(item, i) in genderData" :key="i" style="margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 4px;">
              <span style="color: #191c1e;">{{ item.name }}</span>
              <span style="color: #727780;">{{ pct(item.count) }}% ({{ item.count }})</span>
            </div>
            <div :style="barStyle(item.color, pct(item.count))">
              <div :style="{ height: '100%', width: `${pct(item.count)}%`, background: item.color, borderRadius: '4px' }"></div>
            </div>
          </div>
        </div>

        <!-- Membership -->
        <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e0e3e5;">
          <h3 style="font-size: 14px; font-weight: 700; color: #002a4d; margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 1px solid #e0e3e5;">توزيع العضوية</h3>
          <div style="display: flex; justify-content: space-around; padding: 16px 0; background: #f8f9fa; border-radius: 8px; margin-bottom: 16px;">
            <div style="text-align: center;">
              <div style="font-size: 28px;">📐</div>
              <div style="font-size: 12px; color: #727780; font-weight: 600; margin-top: 4px;">مهندسون</div>
              <div style="font-size: 22px; font-weight: 700; color: #002a4d;">{{ engineersCount }}</div>
            </div>
            <div style="width: 1px; background: #e0e3e5;"></div>
            <div style="text-align: center;">
              <div style="font-size: 28px;">🎓</div>
              <div style="font-size: 12px; color: #727780; font-weight: 600; margin-top: 4px;">خريجون</div>
              <div style="font-size: 22px; font-weight: 700; color: #002a4d;">{{ nonEngineersCount }}</div>
            </div>
          </div>
          
          <div v-for="(item, i) in membershipData" :key="i" style="margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 4px;">
              <span style="color: #191c1e;">{{ item.name }}</span>
              <span style="color: #727780;">{{ pct(item.count) }}% ({{ item.count }})</span>
            </div>
            <div :style="barStyle(item.color, pct(item.count))">
              <div :style="{ height: '100%', width: `${pct(item.count)}%`, background: item.color, borderRadius: '4px' }"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Geographic -->
      <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e0e3e5;">
        <h3 style="font-size: 14px; font-weight: 700; color: #002a4d; margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 1px solid #e0e3e5;">التوزيع الجغرافي</h3>
        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
            <thead>
              <tr style="border-bottom: 1px solid #e0e3e5;">
                <th style="padding: 10px 16px; font-weight: 600; font-size: 11px; color: #727780;">القضاء</th>
                <th style="padding: 10px 16px; font-weight: 600; font-size: 11px; color: #727780;">العدد</th>
                <th style="padding: 10px 16px; font-weight: 600; font-size: 11px; color: #727780;">النسبة</th>
                <th style="padding: 10px 16px; font-weight: 600; font-size: 11px; color: #727780; width: 40%;">التمثيل</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(addr, i) in addressData" :key="i" style="border-bottom: 1px solid #f2f4f6;">
                <td style="padding: 12px 16px; font-weight: 600; color: #002a4d;">{{ addr.name }}</td>
                <td style="padding: 12px 16px; color: #42474f;">{{ addr.count }}</td>
                <td style="padding: 12px 16px; font-weight: 600; color: #002a4d;">{{ addr.pct }}%</td>
                <td style="padding: 12px 16px;">
                  <div style="height: 6px; background: #f2f4f6; border-radius: 3px; overflow: hidden;">
                    <div :style="{ height: '100%', width: `${addr.pct}%`, background: '#002a4d', borderRadius: '3px' }"></div>
                  </div>
                </td>
              </tr>
              <tr v-if="addressData.length === 0">
                <td colspan="4" style="padding: 20px; text-align: center; color: #727780;">لا توجد بيانات جغرافية</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const loading = ref(true);
const registrations = ref([]);

const total = computed(() => registrations.value.length);
const maleCount = computed(() => registrations.value.filter(r => r.gender === 'ذكر').length);
const femaleCount = computed(() => registrations.value.filter(r => r.gender === 'أنثى').length);
const engineersCount = computed(() => registrations.value.filter(r => r.is_engineer).length);
const nonEngineersCount = computed(() => total.value - engineersCount.value);

const pct = (n) => total.value > 0 ? Math.round((n / total.value) * 100) : 0;

const genderData = computed(() => [
  { name: 'الذكور', count: maleCount.value, color: '#002a4d' },
  { name: 'الإناث', count: femaleCount.value, color: '#bcd228' }
]);

const membershipData = computed(() => [
  { name: 'مهندس نقابي', count: engineersCount.value, color: '#002a4d' },
  { name: 'خريج بكالوريوس', count: nonEngineersCount.value, color: '#727780' }
]);

const addressData = computed(() => {
  const counts = {};
  registrations.value.forEach(r => {
    if (!r.address) return;
    const parts = r.address.split('/');
    const a = (parts.length > 1 ? parts[1] : parts[0]).trim();
    counts[a] = (counts[a] || 0) + 1;
  });
  
  return Object.entries(counts)
    .map(([name, count]) => ({ name, count, pct: pct(count) }))
    .sort((a, b) => b.count - a.count);
});

const barStyle = (color, width) => ({
  height: '8px', 
  background: '#f2f4f6', 
  borderRadius: '4px', 
  overflow: 'hidden', 
  marginTop: '4px'
});

const print = () => window.print();

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
  return match ? decodeURIComponent(match[3]) : null;
}

onMounted(async () => {
  try {
    const token = getCookie("admin_token");
    // We'll fetch all registrations for accurate reports (using a large per_page)
    const res = await fetch("/api/v1/admin/registrations?per_page=1000", {
      headers: {
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
      }
    });
    const data = await res.json();
    if (data.data) {
      registrations.value = data.data;
    }
  } catch (err) {
    // silently ignore
  } finally {
    loading.value = false;
  }
});
</script>
