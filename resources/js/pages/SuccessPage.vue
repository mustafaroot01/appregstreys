<template>
  <main style="min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 16px; background: linear-gradient(180deg, #f7f9fb 0%, #ffffff 100%); font-family: 'IBM Plex Sans Arabic', 'Tajawal', sans-serif; direction: rtl; position: relative; overflow: hidden;">
    
    <!-- Background decorations -->
    <div style="position: absolute; top: -10%; left: -10%; width: 40%; height: 40%; border-radius: 50%; background: rgba(0,42,77,0.03); filter: blur(100px); pointer-events: none;"></div>
    <div style="position: absolute; bottom: -10%; right: -10%; width: 50%; height: 50%; border-radius: 50%; background: rgba(188,210,40,0.05); filter: blur(120px); pointer-events: none;"></div>

    <div style="width: 100%; max-width: 440px; text-align: center; position: relative; z-index: 10;">
      
      <!-- Success Animation -->
      <div style="margin-bottom: 32px;">
        <div style="width: 96px; height: 96px; margin: 0 auto; border-radius: 50%; background: #eff6ff; border: 4px solid #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(29,78,216,0.15);">
          <svg style="width: 48px; height: 48px; color: #1d4ed8;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        </div>
      </div>

      <!-- Card -->
      <div id="registration-receipt" style="background: #fff; padding: 32px; border-radius: 24px; border: 1px solid #e0e3e5; box-shadow: 0 10px 40px rgba(0,42,77,0.05); margin-bottom: 24px;">
        <!-- Logo -->
        <div style="display: flex; justify-content: center; margin-bottom: 24px;">
          <div style="position: relative; width: 64px; height: 64px; border-radius: 16px; overflow: hidden; border: 1px solid #e0e3e5;">
            <img src="/images/login-logo.jpg" alt="iT-Diyala" style="object-fit: cover; width: 100%; height: 100%;" />
          </div>
        </div>

        <h1 style="font-size: clamp(24px, 4vw, 32px); font-weight: 900; color: #002a4d; margin-bottom: 8px; letter-spacing: -0.5px;">
          تم التسجيل بنجاح!
        </h1>
        <p style="color: #727780; font-size: 14px; margin-bottom: 24px; line-height: 1.6;">
          تم استلام طلبك بنجاح وسنقوم بمراجعة معلوماتك قريباً.
        </p>

        <!-- Order ID -->
        <div style="background: #f8f9fa; border-radius: 16px; padding: 20px; margin-bottom: 24px; border: 1px dashed #c2c7d0;">
          <p style="font-size: 13px; color: #727780; font-weight: 700; margin-bottom: 4px;">رقم الطلب</p>
          <p style="font-size: 28px; font-weight: 900; color: #002a4d; letter-spacing: 2px; margin: 0;" dir="ltr">
            {{ orderId }}
          </p>
        </div>

        <!-- Status -->
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 100px; background: #ecfdf5; border: 1px solid #a7f3d0; margin-bottom: 24px;">
          <span style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></span>
          <span style="font-size: 13px; font-weight: 800; color: #047857;">حالة الطلب: جديد</span>
        </div>

        <!-- Info -->
        <div style="border-top: 1px solid #e0e3e5; padding-top: 20px;">
          <div style="display: flex; align-items: center; justify-content: center; gap: 8px; color: #727780; font-size: 13px; margin-bottom: 8px;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>تاريخ التسجيل: {{ dateStr }}</span>
          </div>
          <p style="font-size: 12px; color: #9ca3af; margin: 0;">
            يرجى الاحتفاظ برقم الطلب لمتابعة حالة القبول لاحقاً.
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div style="display: flex; flex-direction: column; gap: 12px;" class="no-print">
        <div style="display: flex; gap: 12px;">
          <button @click="handlePrint" style="flex: 1; padding: 14px 0; font-size: 14px; font-weight: 700; color: #42474f; background: #fff; border: 1px solid #c2c7d0; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-family: inherit;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
            </svg>
            طباعة الإشعار
          </button>
          <router-link to="/" style="flex: 1; padding: 14px 0; font-size: 14px; font-weight: 700; color: #fff; background: #002a4d; border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; text-decoration: none;">
            العودة للرئيسية
          </router-link>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const orderId = ref(route.query.orderId || "ITD-XXXXXX");
const dateStr = ref("");

onMounted(() => {
  dateStr.value = new Date().toLocaleDateString("ar-IQ", { 
    year: "numeric", month: "long", day: "numeric", hour: "2-digit", minute: "2-digit" 
  });
});

const handlePrint = () => {
  window.print();
};
</script>

<style>
@media print {
  body * { visibility: hidden; }
  #registration-receipt, #registration-receipt * { visibility: visible; }
  #registration-receipt { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
  .no-print { display: none !important; }
}
</style>
