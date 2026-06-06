<template>
  <main style="min-height: 100vh; display: flex; font-family: 'IBM Plex Sans Arabic', 'Tajawal', sans-serif; direction: rtl;">
    <!-- Left side — Logo/Brand image -->
    <div class="lg-brand-panel" style="display: none; position: relative; overflow: hidden;">
      <img src="/images/login-logo.jpg" alt="iT-Diyala" style="object-fit: cover; width: 100%; height: 100%;" />
      <!-- Dark overlay -->
      <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(0,42,77,0.6) 0%, rgba(0,42,77,0.3) 100%);"></div>
      <!-- Text overlay -->
      <div style="position: absolute; bottom: 40px; right: 40px; left: 40px; z-index: 10; color: #fff;">
        <h2 style="font-size: 28px; font-weight: 700; margin: 0; line-height: 1.3;">بوابة القبول والتحقق الإلكتروني</h2>
      </div>
    </div>

    <!-- Right side — Login form -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 48px 24px; background: #f7f9fb; position: relative; overflow: hidden;">
      <!-- Subtle background blur -->
      <div style="position: absolute; top: -20%; left: -15%; width: 50%; height: 50%; border-radius: 50%; background: rgba(0,42,77,0.03); filter: blur(80px); pointer-events: none;"></div>
      <div style="position: absolute; bottom: -20%; right: -15%; width: 50%; height: 50%; border-radius: 50%; background: rgba(188,210,40,0.05); filter: blur(100px); pointer-events: none;"></div>

      <div style="width: 100%; max-width: 400px; position: relative; z-index: 10;">
        <!-- Logo — visible on mobile -->
        <div style="text-align: center; margin-bottom: 32px;">
          <div style="position: relative; width: 80px; height: 80px; margin: 0 auto 16px; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,42,77,0.12);">
            <img src="/images/login-logo.jpg" alt="iT-Diyala" style="object-fit: cover; width: 100%; height: 100%;" />
          </div>
          <h1 style="font-size: 26px; font-weight: 700; color: #002a4d; margin: 0;">بوابة الإدارة</h1>
          <p style="font-size: 14px; color: #727780; margin-top: 6px;">تسجيل الدخول للمسؤولين</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" style="background: #ffffff; padding: 28px; border-radius: 16px; border: 1px solid #e0e3e5; box-shadow: 0 8px 32px rgba(0,42,77,0.06); display: flex; flex-direction: column; gap: 20px;">
          
          <div v-if="error" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; color: #991b1b; display: flex; align-items: center; gap: 8px;">
            <span>⚠️</span> {{ error }}
          </div>

          <!-- Email -->
          <div>
            <label style="display: block; font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 8px;">
              البريد الإلكتروني
            </label>
            <input
              type="email"
              placeholder=""
              v-model="email"
              class="form-input ltr-input"
            />
          </div>

          <!-- Password -->
          <div>
            <label style="display: block; font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 8px;">
              كلمة المرور
            </label>
            <div style="position: relative;">
              <input
                :type="showPass ? 'text' : 'password'"
                placeholder="••••••••"
                v-model="password"
                class="form-input ltr-input"
                style="padding-left: 44px;"
              />
              <button
                type="button"
                @click="showPass = !showPass"
                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; padding: 4px; display: flex; align-items: center;"
              >
                <svg v-if="showPass" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
                <svg v-else style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading"
            class="submit-btn"
            :style="{ background: loading ? '#4b6a82' : '#002a4d', opacity: loading ? 0.7 : 1, cursor: loading ? 'not-allowed' : 'pointer' }"
          >
            <template v-if="loading">
              <svg style="width: 20px; height: 20px; animation: spin 1s linear infinite;" viewBox="0 0 24 24">
                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              <span>جاري تسجيل الدخول...</span>
            </template>
            <template v-else>
              <span>تسجيل الدخول</span>
              <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
              </svg>
            </template>
          </button>
        </form>

        <p style="text-align: center; font-size: 11px; color: #9ca3af; margin-top: 24px; font-weight: 600;">
          iT-Diyala Central Portal v1.0
        </p>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const showPass = ref(false);

const handleLogin = async () => {
  error.value = "";
  if (!email.value || !password.value) {
    error.value = "يرجى إدخال البريد الإلكتروني وكلمة المرور";
    return;
  }
  
  loading.value = true;
  
  try {
    const res = await fetch("/api/v1/admin/login", {
      method: "POST",
      headers: { "Content-Type": "application/json", "Accept": "application/json" },
      body: JSON.stringify({ email: email.value, password: password.value }),
    });
    const data = await res.json();
    
    if (!res.ok || !data.success) {
      error.value = data.message || "بيانات الدخول غير صحيحة";
      loading.value = false;
      return;
    }
    
    // Store token in cookie (more secure than localStorage)
    document.cookie = `admin_token=${data.data.token}; path=/; max-age=${12*60*60}; SameSite=Strict`;
    router.push("/x-ctrl-7d3k");
  } catch (err) {
    error.value = "حدث خطأ في الاتصال بالخادم";
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@keyframes spin { 
  to { transform: rotate(360deg); } 
}

@media (min-width: 1024px) {
  .lg-brand-panel {
    display: flex !important;
    flex: 0 0 45%;
    max-width: 560px;
  }
}

.form-input {
  width: 100%;
  padding: 12px 14px;
  font-size: 14px;
  font-family: monospace;
  border-radius: 10px;
  border: 1px solid #d1d5db;
  background: #f9fafb;
  outline: none;
  color: #191c1e;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  border-color: #002a4d;
  box-shadow: 0 0 0 3px rgba(0,42,77,0.1);
}

.ltr-input {
  direction: ltr;
  text-align: left;
}

.submit-btn {
  width: 100%;
  padding: 13px 0;
  font-size: 15px;
  font-weight: 700;
  color: #fff;
  border: none;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: inherit;
  transition: background 0.2s;
}
</style>
