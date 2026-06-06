<template>
  <div style="min-height: 100vh; display: flex; flex-direction: column; font-family: 'IBM Plex Sans Arabic', 'Tajawal', sans-serif; direction: rtl; background: linear-gradient(180deg, #f7f9fb 0%, #ffffff 100%); color: #191c1e; position: relative;">
    <!-- Background decorations -->
    <div style="position: absolute; top: 0; left: 0; width: 35%; height: 35%; border-radius: 50%; background: rgba(0,42,77,0.03); filter: blur(100px); pointer-events: none;"></div>
    <div style="position: absolute; bottom: 0; right: 0; width: 40%; height: 40%; border-radius: 50%; background: rgba(188,210,40,0.05); filter: blur(120px); pointer-events: none;"></div>

    <header style="padding: 16px; display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 10;">
      <router-link to="/" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
        <div style="position: relative; width: 40px; height: 40px; border-radius: 12px; overflow: hidden;">
          <img src="/images/login-logo.jpg" alt="iT-Diyala" style="object-fit: cover; width: 100%; height: 100%;" />
        </div>
        <span style="font-size: 16px; font-weight: 800; color: #002a4d;">العودة للرئيسية</span>
      </router-link>
    </header>

    <main style="flex: 1; padding: 24px 16px 80px; display: flex; justify-content: center; position: relative; z-index: 10;">
      <div style="width: 100%; max-width: 640px; background: #fff; border-radius: 24px; padding: clamp(20px, 5vw, 40px); box-shadow: 0 20px 40px rgba(0,42,77,0.06); border: 1px solid rgba(0,42,77,0.05);">
        <!-- Header of the form block -->
        <div style="text-align: center; margin-bottom: 32px;">
          <h1 style="font-size: clamp(20px, 5vw, 26px); font-weight: 800; color: #002a4d; margin: 0;">استمارة التسجيل الإلكترونية</h1>
          <p style="color: #727780; font-size: clamp(13px, 3vw, 14px); margin-top: 8px;">يرجى إدخال بياناتك بدقة لإتمام عملية التسجيل بنجاح.</p>
        </div>

        <ProgressBar :step="step" />
        
        <div v-if="globalError" style="background: #fef2f2; color: #991b1b; padding: 12px 16px; border-radius: 10px; border: 1px solid #fecaca; margin-bottom: 16px; font-weight: 600;">
          {{ globalError }}
        </div>

        <div :style="{ opacity: isSubmitting ? '0.6' : '1', pointerEvents: isSubmitting ? 'none' : 'auto' }">
          <Step1
            v-if="step === 1"
            :data="formData"
            :errors="errors"
            @update="updateField"
            @file="updateFile"
            @next="handleNextStep1"
          />
          
          <Step2
            v-if="step === 2"
            :phone="formData.phone"
            @back="step = 1"
            @verify="submitForm"
          />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import ProgressBar from './components/ProgressBar.vue';
import Step1 from './components/Step1.vue';
import Step2 from './components/Step2.vue';

const router = useRouter();
const step = ref(1);
const isSubmitting = ref(false);
const globalError = ref('');

const formData = reactive({
  fullName: "",
  phone: "",
  address: "",
  gender: "",
  studyLevel: "",
  isEmployee: "",
  isEngineer: "لا",
  engineerIdImage: null,
  whatsappPhone: "",
  email: "",
  aboutMe: "",
  trainingType: ""
});

const errors = reactive({});

const updateField = (field, value) => {
  formData[field] = value;
  errors[field] = '';
};

const updateFile = (file) => {
  formData.engineerIdImage = file;
  errors.engineerIdImage = '';
};

function validateIraqiPhone(phone) {
  const cleaned = phone.replace(/[\s\-\(\)]/g, "");
  return /^(\+?964|0)?7[0-9]{9}$/.test(cleaned);
}

function validateName(name) {
  if (!name.trim()) return "الاسم الثلاثي مطلوب";
  if (/\d/.test(name)) return "الاسم لا يجب أن يحتوي على أرقام";
  if (/[!@#$%^&*()_+=\[\]{};':"\\|,.<>\/?`~]/.test(name)) return "الاسم يحتوي على رموز غير مسموحة";
  const parts = name.trim().split(/\s+/);
  if (parts.length < 3) return "يجب إدخال الاسم الثلاثي كاملاً";
  return null;
}

const handleNextStep1 = async () => {
  const errs = {};
  const nameErr = validateName(formData.fullName);
  if (nameErr) errs.fullName = nameErr;
  if (!validateIraqiPhone(formData.phone)) errs.phone = "رقم هاتف عراقي غير صالح";
  if (!formData.address.trim()) errs.address = "يرجى تحديد عنوان السكن";
  if (!formData.gender) errs.gender = "يرجى تحديد الجنس";
  if (!formData.studyLevel) errs.studyLevel = "يرجى تحديد نوع الدراسة";
  if (!formData.trainingType) errs.trainingType = "يرجى تحديد نوع التدريب";
  if (!formData.isEmployee) errs.isEmployee = "يرجى تحديد ما إذا كنت موظفاً أم لا";
  if (formData.isEngineer === "نعم" && !formData.engineerIdImage) errs.engineerIdImage = "صورة الهوية مطلوبة لمهندسي النقابة";
  if (formData.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
    errs.email = "صيغة البريد الإلكتروني غير صحيحة";
  }

  Object.keys(errors).forEach(key => delete errors[key]);
  Object.assign(errors, errs);

  if (Object.keys(errs).length > 0) {
    window.scrollTo({ top: 0, behavior: "smooth" });
  } else {
    globalError.value = "";
    isSubmitting.value = true;
    try {
      const res = await fetch("/api/v1/otp/send", {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        body: JSON.stringify({ phone: formData.phone }),
      });
      const respData = await res.json();
      
      if (!res.ok || !respData.success) {
        globalError.value = respData.message || "فشل في إرسال الرمز";
        isSubmitting.value = false;
        return;
      }
      
      step.value = 2;
      window.scrollTo({ top: 0, behavior: "smooth" });
    } catch (err) {
      globalError.value = "خطأ في الاتصال بالخادم";
    } finally {
      isSubmitting.value = false;
    }
  }
};

const submitForm = async () => {
  globalError.value = "";
  const data = new FormData();
  data.append("full_name", formData.fullName);
  data.append("phone", formData.phone);
  data.append("whatsapp_phone", formData.whatsappPhone || formData.phone);
  if (formData.email) data.append("email", formData.email);
  data.append("address", formData.address);
  data.append("gender", formData.gender);
  data.append("study_level", formData.studyLevel);
  data.append("training_type", formData.trainingType);
  if (formData.aboutMe) data.append("about_me", formData.aboutMe);
  data.append("is_employee", formData.isEmployee);
  data.append("is_engineer", formData.isEngineer);
  if (formData.engineerIdImage) {
    data.append("engineer_id_image", formData.engineerIdImage);
  }

  try {
    const res = await fetch("/api/v1/register", {
      method: "POST",
      headers: { "Accept": "application/json" },
      body: data,
    });
    const respData = await res.json();
    if (!res.ok || !respData.success) {
      globalError.value = respData.message || "حدث خطأ أثناء التسجيل";
      step.value = 1;
    } else {
      router.push({ path: "/success", query: { orderId: respData.data?.order_id } });
    }
  } catch (err) {
    globalError.value = "خطأ في الاتصال بالخادم";
    step.value = 1;
  }
};
</script>
