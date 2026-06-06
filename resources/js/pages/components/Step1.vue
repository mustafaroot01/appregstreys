<template>
  <div style="display: flex; flex-direction: column; gap: clamp(14px, 4vw, 20px);">
    <!-- Full Name -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">الاسم الثلاثي للطالب <span style="color: #ef4444;">*</span></label>
      <input
        type="text"
        placeholder="مثال: أحمد محمد علي"
        :value="data.fullName"
        @input="$emit('update', 'fullName', $event.target.value)"
        class="form-input"
        :style="{ borderColor: errors.fullName ? '#ef4444' : '#d1d5db' }"
      />
      <p v-if="errors.fullName" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.fullName }}</p>
    </div>

    <!-- Phone -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">رقم الهاتف <span style="color: #ef4444;">*</span></label>
      <input
        type="tel"
        placeholder="07XXXXXXXXX"
        :value="data.phone"
        @input="$emit('update', 'phone', $event.target.value.replace(/[^\d+]/g, ''))"
        dir="ltr"
        class="form-input ltr-input"
        :style="{ borderColor: errors.phone ? '#ef4444' : '#d1d5db' }"
      />
      <p v-if="errors.phone" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.phone }}</p>
    </div>

    <!-- Email -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">البريد الإلكتروني <span style="color: #727780; font-weight: 400;">(اختياري)</span></label>
      <input
        type="email"
        placeholder="example@domain.com"
        :value="data.email"
        @input="$emit('update', 'email', $event.target.value)"
        dir="ltr"
        class="form-input ltr-input"
        :style="{ borderColor: errors.email ? '#ef4444' : '#d1d5db' }"
      />
      <p v-if="errors.email" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.email }}</p>
    </div>

    <!-- Address -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">عنوان السكن <span style="color: #ef4444;">*</span></label>
      <input
        type="text"
        placeholder="المحافظة / المدينة / الحي"
        :value="data.address"
        @input="$emit('update', 'address', $event.target.value)"
        class="form-input"
        :style="{ borderColor: errors.address ? '#ef4444' : '#d1d5db' }"
      />
      <p v-if="errors.address" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.address }}</p>
    </div>

    <!-- Gender -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">الجنس <span style="color: #ef4444;">*</span></label>
      <select
        :value="data.gender"
        @change="$emit('update', 'gender', $event.target.value)"
        class="form-input"
        :style="{ cursor: 'pointer', borderColor: errors.gender ? '#ef4444' : '#d1d5db' }"
      >
        <option value="" disabled>اختر الجنس</option>
        <option value="ذكر">ذكر</option>
        <option value="أنثى">أنثى</option>
      </select>
      <p v-if="errors.gender" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.gender }}</p>
    </div>

    <!-- Study Level -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">نوع الدراسة <span style="color: #ef4444;">*</span></label>
      <select
        :value="data.studyLevel"
        @change="$emit('update', 'studyLevel', $event.target.value)"
        class="form-input"
        :style="{ cursor: 'pointer', borderColor: errors.studyLevel ? '#ef4444' : '#d1d5db' }"
      >
        <option value="" disabled>اختر نوع الدراسة</option>
        <option value="طالب اعدادي">طالب اعدادي</option>
        <option value="دبلوم">دبلوم</option>
        <option value="بكلوريوس">بكلوريوس</option>
        <option value="ماجستير">ماجستير</option>
        <option value="دكتوراه">دكتوراه</option>
        <option value="غير ذلك">غير ذلك</option>
      </select>
      <p v-if="errors.studyLevel" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.studyLevel }}</p>
    </div>

    <!-- Training Type -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">نوع التدريب <span style="color: #ef4444;">*</span></label>
      <select
        :value="data.trainingType"
        @change="$emit('update', 'trainingType', $event.target.value)"
        class="form-input"
        :style="{ cursor: 'pointer', borderColor: errors.trainingType ? '#ef4444' : '#d1d5db' }"
      >
        <option value="" disabled>اختر نوع التدريب</option>
        <option value="حضوري">حضوري (قاعة نقابة المهندسين)</option>
        <option value="اون لاين">اون لاين</option>
      </select>
      <p v-if="errors.trainingType" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.trainingType }}</p>
    </div>

    <!-- Employee Status -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">هل أنت موظف؟ <span style="color: #ef4444;">*</span></label>
      <select
        :value="data.isEmployee"
        @change="$emit('update', 'isEmployee', $event.target.value)"
        class="form-input"
        :style="{ cursor: 'pointer', borderColor: errors.isEmployee ? '#ef4444' : '#d1d5db' }"
      >
        <option value="" disabled>اختر الحالة</option>
        <option value="نعم">نعم</option>
        <option value="لا">لا</option>
      </select>
      <p v-if="errors.isEmployee" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.isEmployee }}</p>
    </div>

    <!-- Engineer -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">هل أنت منتمي إلى نقابة المهندسين؟</label>
      <div style="display: flex; gap: 12px;">
        <button
          v-for="opt in ['نعم', 'لا']"
          :key="opt"
          type="button"
          @click="() => { $emit('update', 'isEngineer', opt); if (opt === 'لا') $emit('file', null); }"
          class="btn-toggle"
          :class="{ active: data.isEngineer === opt }"
        >
          {{ opt === "نعم" ? "✓" : "✗" }} {{ opt }}
        </button>
      </div>
    </div>

    <!-- Engineer ID Upload -->
    <div v-if="data.isEngineer === 'نعم'">
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">صورة هوية نقابة المهندسين <span style="color: #ef4444;">*</span></label>
      <div
        @click="fileInput.click()"
        style="cursor: pointer; border: 2px dashed; border-radius: 12px; padding: 24px; text-align: center; background: #f8f9fa; transition: all 0.2s;"
        :style="{ borderColor: errors.engineerIdImage ? '#ef4444' : '#c2c7d0' }"
      >
        <div v-if="preview" style="position: relative; width: 100%; height: 160px; border-radius: 8px; overflow: hidden;">
          <img :src="preview" alt="صورة الهوية" style="object-fit: contain; width: 100%; height: 100%;" />
        </div>
        <div v-else>
          <div style="font-size: 32px; margin-bottom: 8px;">📁</div>
          <p style="font-size: 13px; color: #002a4d; font-weight: 600; margin: 0;">انقر هنا لرفع صورة الهوية</p>
          <p style="font-size: 11px; color: #727780; margin-top: 4px;">JPG, PNG أو WEBP بحد أقصى 10MB</p>
        </div>
      </div>
      <input
        ref="fileInput"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        style="display: none;"
        @change="handleFileChange"
      />
      <p v-if="errors.engineerIdImage" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.engineerIdImage }}</p>
    </div>

    <!-- About Me -->
    <div>
      <label style="display: block; font-size: 13px; font-weight: 700; color: #002a4d; margin-bottom: 8px;">تحدث عن نفسك <span style="color: #727780; font-weight: 400;">(اختياري)</span></label>
      <textarea
        placeholder="اكتب نبذة مختصرة عنك..."
        :value="data.aboutMe"
        @input="$emit('update', 'aboutMe', $event.target.value)"
        rows="4"
        class="form-input"
        style="resize: vertical;"
        :style="{ borderColor: errors.aboutMe ? '#ef4444' : '#d1d5db' }"
      ></textarea>
      <p v-if="errors.aboutMe" style="color: #ef4444; font-size: 11px; margin-top: 6px; font-weight: 600;">⚠ {{ errors.aboutMe }}</p>
    </div>

    <!-- Next Button -->
    <button type="button" @click="$emit('next')" class="btn-next">
      المتابعة
      <svg style="width: 20px; height: 20px; transform: rotate(180deg);" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue';

const props = defineProps({
  data: Object,
  errors: Object
});

const emit = defineEmits(['update', 'file', 'next']);

const fileInput = ref(null);
const preview = ref(null);

watch(() => props.data.engineerIdImage, (newFile) => {
  if (newFile) {
    preview.value = URL.createObjectURL(newFile);
  } else {
    if (preview.value) URL.revokeObjectURL(preview.value);
    preview.value = null;
  }
});

onUnmounted(() => {
  if (preview.value) URL.revokeObjectURL(preview.value);
});

const handleFileChange = (e) => {
  const file = e.target.files?.[0] ?? null;
  if (file) {
    if (!file.type.startsWith("image/")) {
      alert("يرجى اختيار ملف صورة فقط.");
      return;
    }
    if (file.size > 10 * 1024 * 1024) {
      alert("حجم الصورة كبير جداً، يجب ألا يتجاوز 10 ميجابايت");
      return;
    }
  }
  emit('file', file);
};
</script>

<style scoped>
.form-input {
  width: 100%;
  padding: 12px 14px;
  font-size: 14px;
  border-radius: 10px;
  border: 1px solid #d1d5db;
  background: #f9fafb;
  outline: none;
  color: #191c1e;
  text-align: right;
  font-family: inherit;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus {
  border-color: #002a4d !important;
}

.ltr-input {
  font-family: monospace;
  text-align: left;
}

.btn-toggle {
  padding: 12px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  border: 1px solid #d1d5db;
  cursor: pointer;
  text-align: center;
  font-family: inherit;
  flex: 1;
  transition: all 0.2s;
  background: #fff;
  color: #42474f;
}

.btn-toggle.active {
  background: #eff6ff;
  border-color: #3b82f6;
  color: #1d4ed8;
}

.btn-next {
  width: 100%;
  padding: 14px 0;
  font-size: 15px;
  font-weight: 700;
  color: #fff;
  background: #002a4d;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: inherit;
  margin-top: 16px;
}
</style>
