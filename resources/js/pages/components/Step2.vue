<template>
  <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 20px;">
    <div style="width: 80px; height: 80px; border-radius: 50%; background: #eff6ff; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 8px;">
      📱
    </div>
    <div>
      <h2 style="font-size: 20px; font-weight: 800; color: #002a4d; margin-bottom: 8px;">التحقق من رقم الهاتف</h2>
      <p style="font-size: 14px; color: #727780; line-height: 1.6;">أرسلنا رمز تحقق مكون من 6 أرقام إلى الرقم:<br/><span style="color: #002a4d; font-weight: 700; font-family: monospace;" dir="ltr">{{ phone }}</span></p>
    </div>

    <div style="display: flex; gap: clamp(4px, 2vw, 8px); justify-content: center; direction: ltr; margin: 16px 0;">
      <input
        v-for="(digit, i) in otp"
        :key="i"
        ref="inputsRef"
        type="text"
        inputmode="numeric"
        maxlength="1"
        :value="digit"
        @input="onInput(i, $event)"
        @keydown="onKeyDown(i, $event)"
        class="otp-input"
      />
    </div>

    <p v-if="error" style="color: #ef4444; font-size: 13px; font-weight: 600; margin: 0;">⚠ {{ error }}</p>
    <p v-if="resendMessage" style="color: #047857; font-size: 13px; font-weight: 600; margin: 0;">✓ {{ resendMessage }}</p>

    <div style="font-size: 13px; color: #727780; margin-top: -8px;">
      لم تستلم الرمز؟ 
      <button 
        @click="handleResend"
        :disabled="resendCooldown > 0 || isResending"
        :style="{ 
          background: 'none', border: 'none', 
          color: resendCooldown > 0 ? '#c2c7d0' : '#1d4ed8', 
          fontWeight: 700, cursor: resendCooldown > 0 ? 'not-allowed' : 'pointer', 
          padding: 0, textDecoration: resendCooldown > 0 ? 'none' : 'underline'
        }"
      >
        {{ isResending ? 'جاري الإرسال...' : (resendCooldown > 0 ? `أعد الإرسال بعد ${resendCooldown} ثانية` : 'إعادة إرسال') }}
      </button>
    </div>

    <div style="display: flex; gap: 12px; width: 100%; margin-top: 8px;">
      <button @click="$emit('back')" :disabled="loading" style="flex: 1; padding: 14px 0; font-size: 14px; font-weight: 700; color: #42474f; background: #fff; border: 1px solid #c2c7d0; border-radius: 10px; cursor: pointer; font-family: inherit;">
        رجوع
      </button>
      <button @click="handleVerify" :disabled="loading" style="flex: 2; padding: 14px 0; font-size: 14px; font-weight: 700; color: #fff; background: #002a4d; border: none; border-radius: 10px; cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 8px;" :style="{ opacity: loading ? 0.7 : 1, cursor: loading ? 'not-allowed' : 'pointer' }">
        {{ loading ? 'جاري التحقق...' : 'تأكيد الرمز' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  phone: String
});

const emit = defineEmits(['back', 'verify']);

const otp = ref(['', '', '', '', '', '']);
const loading = ref(false);
const error = ref('');
const resendCooldown = ref(25);
const isResending = ref(false);
const resendMessage = ref('');
const inputsRef = ref([]);

let timer;

onMounted(() => {
  if (resendCooldown.value > 0) {
    timer = setInterval(() => {
      if (resendCooldown.value > 0) resendCooldown.value--;
    }, 1000);
  }
});

onUnmounted(() => {
  clearInterval(timer);
});

const onInput = (i, e) => {
  const val = e.target.value.replace(/\D/g, "");
  otp.value[i] = val;
  if (val && i < 5) {
    inputsRef.value[i + 1]?.focus();
  }
};

const onKeyDown = (i, e) => {
  if (e.key === "Backspace" && !otp.value[i] && i > 0) {
    inputsRef.value[i - 1]?.focus();
  }
};

const handleVerify = async () => {
  const code = otp.value.join("");
  if (code.length < 6) { error.value = "يرجى إدخال الرمز كاملاً (6 أرقام)"; return; }
  loading.value = true; 
  error.value = "";
  
  try {
    const res = await fetch("/api/v1/otp/verify", {
      method: "POST",
      headers: { "Content-Type": "application/json", "Accept": "application/json" },
      body: JSON.stringify({ phone: props.phone, code }),
    });
    const data = await res.json();
    if (!res.ok || !data.success) {
      error.value = data.message || "الرمز غير صحيح، يرجى المحاولة مرة أخرى";
    } else {
      emit('verify');
    }
  } catch (err) {
    error.value = "حدث خطأ في الاتصال بالخادم";
  } finally {
    loading.value = false;
  }
};

const handleResend = async () => {
  if (resendCooldown.value > 0 || isResending.value) return;
  isResending.value = true;
  error.value = "";
  resendMessage.value = "";
  
  try {
    const res = await fetch("/api/v1/otp/send", {
      method: "POST",
      headers: { "Content-Type": "application/json", "Accept": "application/json" },
      body: JSON.stringify({ phone: props.phone }),
    });
    const data = await res.json();
    if (!res.ok || !data.success) {
      error.value = data.message || "فشل إرسال الرمز، يرجى المحاولة لاحقاً";
      if (data.data?.cooldown_remaining) {
        resendCooldown.value = data.data.cooldown_remaining;
      }
    } else {
      resendMessage.value = "تم إرسال كود جديد إلى رقمك بنجاح";
      resendCooldown.value = data.data?.cooldown || 25;
      otp.value = ["", "", "", "", "", ""];
      inputsRef.value[0]?.focus();
    }
  } catch (err) {
    error.value = "خطأ في الاتصال بالخادم أثناء إعادة الإرسال";
  } finally {
    isResending.value = false;
  }
};
</script>

<style scoped>
.otp-input {
  width: clamp(36px, 10vw, 48px);
  height: clamp(48px, 12vw, 60px);
  font-size: clamp(18px, 5vw, 24px);
  font-weight: 700;
  text-align: center;
  border-radius: 12px;
  border: 1px solid #c2c7d0;
  background: #f8f9fa;
  outline: none;
  color: #002a4d;
  padding: 0;
}

.otp-input:focus {
  border-color: #002a4d;
  box-shadow: 0 0 0 3px rgba(0,42,77,0.1);
}
</style>
