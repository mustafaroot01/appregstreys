<template>
  <div v-if="loading" style="padding: 40px; text-align: center; font-weight: bold;">
    جاري التحميل...
  </div>

  <div v-else style="display: flex; flex-direction: column; gap: 20px;">
    <div>
      <h1 style="font-size: 22px; font-weight: 700; color: #002a4d; margin: 0;">إعدادات النظام</h1>
      <p style="font-size: 14px; color: #727780; margin-top: 4px;">تعديل خصائص وقيود البوابة الإلكترونية.</p>
    </div>

    <form @submit.prevent="handleSave" style="display: flex; flex-direction: column; gap: 16px;">
      
      <!-- API -->
      <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e0e3e5;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e0e3e5;">
          <span style="font-size: 24px;">🔑</span>
          <div>
            <div style="font-size: 14px; font-weight: 700; color: #002a4d;">إعدادات خدمة الرسائل (OTP)</div>
            <div style="font-size: 11px; color: #727780;">ربط بوابة التحقق من الأرقام</div>
          </div>
        </div>
        <label style="display: block; font-size: 12px; font-weight: 600; color: #42474f; margin-bottom: 6px;">مفتاح API</label>
        <input type="password" v-model="apiKey" class="form-input" style="font-family: monospace; letter-spacing: 1px;" />
        <p style="font-size: 10px; color: #727780; margin-top: 6px;">⚠️ لا تشارك هذا المفتاح.</p>
      </div>

      <!-- Controls -->
      <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e0e3e5;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e0e3e5;">
          <span style="font-size: 24px;">⚙️</span>
          <div>
            <div style="font-size: 14px; font-weight: 700; color: #002a4d;">التحكم العام</div>
            <div style="font-size: 11px; color: #727780;">القيود التشغيلية والسعة القصوى</div>
          </div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 16px;">
          <!-- Toggle -->
          <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e3e5;">
            <div>
              <div style="font-size: 13px; font-weight: 600; color: #191c1e;">حالة التقديم الإلكتروني</div>
              <div style="font-size: 11px; color: #727780; margin-top: 2px;">عند التعطيل لن يُستقبل طلبات</div>
            </div>
            <button
              type="button"
              @click="allowReg = !allowReg"
              style="position: relative; width: 44px; height: 24px; border-radius: 12px; border: none; cursor: pointer; flex-shrink: 0; transition: background 0.2s;"
              :style="{ background: allowReg ? '#002a4d' : '#c2c7d0' }"
            >
              <span style="position: absolute; top: 2px; width: 20px; height: 20px; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.2); transition: right 0.2s, left 0.2s;"
                    :style="{ right: allowReg ? '2px' : 'auto', left: allowReg ? 'auto' : '2px' }"></span>
            </button>
          </div>
          <!-- Limit -->
          <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #42474f; margin-bottom: 6px;">السعة القصوى اليومية</label>
            <input type="number" v-model.number="limit" class="form-input" />
            <p style="font-size: 10px; color: #727780; margin-top: 6px;">يُقفل التسجيل تلقائياً عند الوصول لهذا الحد.</p>
          </div>
        </div>
      </div>

      <!-- Save -->
      <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #fff; border-radius: 12px; border: 1px solid #e0e3e5;">
        <span v-if="saved" style="font-size: 13px; font-weight: 600; color: #047857;">✓ تم الحفظ بنجاح</span>
        <span v-else></span>
        <button type="submit" style="background: #002a4d; color: #fff; border: none; border-radius: 8px; padding: 10px 24px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;">
          حفظ التغييرات
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const apiKey = ref('');
const allowReg = ref(true);
const limit = ref(0);
const saved = ref(false);
const loading = ref(true);

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
  return match ? decodeURIComponent(match[3]) : null;
}

onMounted(async () => {
  try {
    const token = getCookie("admin_token");
    const res = await fetch("/api/v1/admin/settings", {
      headers: { 
        "Authorization": `Bearer ${token}`,
        "Accept": "application/json"
      }
    });
    if (res.status === 401) {
      router.push("/x-ctrl-7d3k/login");
      return;
    }
    const data = await res.json();
    if (data.success && data.data) {
      const registration = data.data.registration || {};
      const otp = data.data.otp || {};

      apiKey.value = otp.otp_api_key?.value || "";
      allowReg.value = registration.registration_open?.value === true || registration.registration_open?.value === "1";
    }
  } catch (err) {
    // silently ignore
  } finally {
    loading.value = false;
  }
});

const handleSave = async () => {
  try {
    const token = getCookie("admin_token");
    const res = await fetch("/api/v1/admin/settings", {
      method: "PUT",
      headers: {
        "Authorization": `Bearer ${token}`,
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        settings: [
          { key: "otp_api_key", value: apiKey.value },
          { key: "registration_open", value: allowReg.value }
        ]
      })
    });
    
    if (res.status === 401) {
      alert("انتهت صلاحية الجلسة، يرجى تسجيل الدخول مجدداً");
      router.push("/x-ctrl-7d3k/login");
      return;
    }
    
    const data = await res.json();
    if (res.ok) {
      saved.value = true;
      setTimeout(() => saved.value = false, 3000);
    } else {
      alert("فشل الحفظ: " + (data.message || "حدث خطأ غير معروف"));
    }
  } catch (err) {
    alert("حدث خطأ أثناء الاتصال بالخادم: " + err.message);
  }
};
</script>

<style scoped>
.form-input {
  width: 100%;
  padding: 10px 12px;
  font-size: 13px;
  border-radius: 8px;
  border: 1px solid #e0e3e5;
  background: #f8f9fa;
  outline: none;
  font-family: inherit;
}

.form-input:focus {
  border-color: #002a4d;
}
</style>
