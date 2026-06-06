<template>
  <div style="min-height: 100vh; display: flex; flex-direction: column; font-family: 'IBM Plex Sans Arabic', 'Tajawal', sans-serif; direction: rtl; background: linear-gradient(180deg, #f7f9fb 0%, #ffffff 100%); color: #191c1e; position: relative;">
    
    <!-- Background decorations -->
    <div style="position: absolute; top: 0; left: 0; width: 40%; height: 40%; border-radius: 50%; background: rgba(0,42,77,0.03); filter: blur(120px); pointer-events: none;"></div>
    <div style="position: absolute; bottom: 0; right: 0; width: 50%; height: 50%; border-radius: 50%; background: rgba(188,210,40,0.05); filter: blur(150px); pointer-events: none;"></div>

    <!-- HEADER -->
    <header style="position: relative; z-index: 50; width: 100%; padding: 16px 16px 0;">
      <nav style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.8); border: 1px solid #e0e3e5; border-radius: 100px; padding: 8px 20px; backdrop-filter: blur(12px); box-shadow: 0 4px 16px rgba(0,42,77,0.04);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="position: relative; width: 40px; height: 40px; border-radius: 12px; overflow: hidden;">
            <img src="/images/login-logo.jpg" alt="iT-Diyala" style="object-fit: cover; width: 100%; height: 100%;" />
          </div>
        </div>
      </nav>
    </header>

    <!-- HERO SECTION -->
    <main style="position: relative; z-index: 10; flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 16px; text-align: center;">
      <div style="max-width: 900px; width: 100%;">
        
        <!-- Badge -->
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 100px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 13px; font-weight: 700; margin-bottom: 24px;">
          <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
          نظام التسجيل المركزي
        </div>

        <!-- Title -->
        <h1 style="font-size: clamp(32px, 6vw, 64px); font-weight: 900; color: #002a4d; line-height: 1.2; margin-bottom: 16px; letter-spacing: -1px;">
          بوابة القبول <br />
          <span style="color: #bcd228;">الإلكترونية</span>
        </h1>
        
        <!-- Subtitle -->
        <p style="font-size: clamp(15px, 2vw, 20px); color: #42474f; line-height: 1.6; max-width: 640px; margin: 0 auto 40px; font-weight: 500;">
          انضم إلى كورس تطوير مواقع وتطبيقات باستخدام الذكاء الاصطناعي وتعلّم خطوة بخطوة كيف تحوّل فكرتك إلى مشروع حقيقي جاهز للنشر.
        </p>

        <!-- CTA Area -->
        <div style="position: relative; padding: 32px 0;">
          <!-- Divider line -->
          <div style="position: absolute; left: 50%; transform: translateX(-50%); top: 50%; width: 100%; height: 1px; background: linear-gradient(90deg, transparent, #e0e3e5, transparent); z-index: 0;"></div>

          <div v-if="isInitializing" style="height: 120px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1;">
            <svg class="animate-spin" width="32" height="32" viewBox="0 0 24 24" fill="none" style="color: #002a4d;">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
              <path fill="currentColor" style="opacity: 0.75;" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
          </div>
          
          <div v-else-if="registrationOpen" style="display: flex; flex-direction: column; align-items: center; gap: 16px; position: relative; z-index: 1;">
            <router-link to="/register" style="display: inline-flex; align-items: center; gap: 12px; padding: 16px 40px; background: #002a4d; color: #fff; font-weight: 800; font-size: 18px; border-radius: 100px; text-decoration: none; box-shadow: 0 10px 30px rgba(0,42,77,0.2); transition: transform 0.2s, box-shadow 0.2s;" onmouseenter="this.style.transform='scale(1.05)'; this.style.boxShadow='0 15px 40px rgba(0,42,77,0.3)'" onmouseleave="this.style.transform='scale(1)'; this.style.boxShadow='0 10px 30px rgba(0,42,77,0.2)'">
              ابدأ التسجيل الآن
              <svg style="width: 24px; height: 24px; transform: rotate(180deg);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </router-link>
            <p style="color: #047857; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; margin: 0;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #047857;"></span>
              البوابة مفتوحة حالياً لاستقبال الطلبات
            </p>
          </div>
          
          <div v-else style="display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 20px 0; position: relative; z-index: 1;">
            <div style="width: 64px; height: 64px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #dc2626; margin-bottom: 8px;">
              <svg style="width: 32px; height: 32px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; color: #191c1e; margin: 0;">التسجيل مغلق حالياً</h3>
            <p style="font-size: 14px; color: #727780; margin: 0; max-width: 480px;">لقد تم إغلاق باب التسجيل أو لم يتم فتحه بعد. يرجى متابعة الإعلانات لمعرفة موعد الافتتاح القادم.</p>
          </div>
        </div>

        <!-- Core Features Grid -->
        <div style="margin-top: 48px; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; text-align: right;">
          <div v-for="(feature, idx) in features" :key="idx" style="padding: 28px; border-radius: 24px; background: #fff; border: 1px solid #e0e3e5; box-shadow: 0 10px 30px rgba(0,42,77,0.03); transition: transform 0.2s;" onmouseenter="this.style.transform='translateY(-5px)'" onmouseleave="this.style.transform='translateY(0)'">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; color: #1d4ed8; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
              <svg style="width: 28px; height: 28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" :d="feature.icon" />
              </svg>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: #002a4d; margin-bottom: 12px;">{{ feature.title }}</h3>
            <p style="color: #727780; font-size: 14px; line-height: 1.6; margin: 0;">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </main>
    
    <footer style="position: relative; z-index: 10; width: 100%; padding: 32px 16px; text-align: center; border-top: 1px solid #e0e3e5; background: #f8f9fa; margin-top: auto;">
      <div style="display: flex; justify-content: center; gap: 16px; margin-bottom: 16px;">
        <!-- Instagram -->
        <a href="https://www.instagram.com/itdiyala" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 1px solid #e0e3e5; color: #E1306C; transition: transform 0.2s;" onmouseenter="this.style.transform='scale(1.1)'" onmouseleave="this.style.transform='scale(1)'">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
          </svg>
        </a>
        <!-- Facebook -->
        <a href="https://www.facebook.com/ITDT.online" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 1px solid #e0e3e5; color: #1877F2; transition: transform 0.2s;" onmouseenter="this.style.transform='scale(1.1)'" onmouseleave="this.style.transform='scale(1)'">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
        </a>
      </div>
      <p style="font-size: 13px; font-weight: 600; color: #727780; margin: 0;">
        جميع الحقوق محفوظة &copy; {{ new Date().getFullYear() }} <span style="color: #002a4d;">iT-Diyala</span>
      </p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const registrationOpen = ref(false);
const isInitializing = ref(true);

const features = [
  { icon: "M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4", title: "بناء مشاريع حقيقية", desc: "تطبيق عملي خطوة بخطوة لبناء تطبيقات ومواقع حديثة باستخدام تقنيات الذكاء الاصطناعي." },
  { icon: "M13 10V3L4 14h7v7l9-11h-7z", title: "أدوات الذكاء الاصطناعي", desc: "تعلم كيفية توظيف أدوات مثل ChatGPT و Claude لتسريع عملية البرمجة والتطوير." },
  { icon: "M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z", title: "من الصفر للاحتراف", desc: "منهج مبسط يبدأ معك من الصفر وحتى إطلاق مشروعك الأول بنجاح." }
];

onMounted(async () => {
  try {
    const res = await fetch("/api/v1/registration/settings", {
      cache: "no-store",
      headers: { "Cache-Control": "no-cache, no-store, must-revalidate", "Pragma": "no-cache" }
    });
    const data = await res.json();
    
    if (data.success && data.data) {
      const isOpen = data.data.registration_open === true || data.data.registration_open === "1";
      registrationOpen.value = isOpen;
    }
  } catch (err) {
    // silently ignore
  } finally {
    isInitializing.value = false;
  }
});
</script>

<style scoped>
.nav-link:hover {
  background: #e0e3e5 !important;
}
.register-btn:hover {
  transform: scale(1.05);
  box-shadow: 0 15px 40px rgba(0,42,77,0.3) !important;
}
.feature-card:hover {
  transform: translateY(-5px);
}
.social-link:hover {
  transform: scale(1.1);
}
.spinner_b2T7 { 
  animation: spinner_Bwwq 1.5s linear infinite; 
}
@keyframes spinner_Bwwq { 
  100% { transform: rotate(360deg); } 
}
</style>
