<template>
  <div style="display: flex; align-items: center; justify-content: center; width: 100%; max-width: 480px; margin: 0 auto 32px;">
    <div v-for="(s, i) in steps" :key="s.num" style="display: flex; align-items: center;" :style="{ flex: i < steps.length - 1 ? 1 : 'none' }">
      <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
        <div style="width: clamp(28px, 8vw, 36px); height: clamp(28px, 8vw, 36px); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: clamp(11px, 3vw, 14px); transition: all 0.3s;"
             :style="{ 
               background: step > s.num || step === s.num ? '#002a4d' : '#e0e3e5',
               color: step > s.num || step === s.num ? '#fff' : '#727780',
               boxShadow: step === s.num ? '0 0 0 4px rgba(0,42,77,0.1)' : 'none'
             }">
          {{ step > s.num ? "✓" : s.num }}
        </div>
        <span style="font-size: clamp(9px, 2.5vw, 11px); white-space: nowrap;"
              :style="{ 
                fontWeight: step === s.num ? 700 : 600, 
                color: step === s.num || step > s.num ? '#002a4d' : '#727780' 
              }">
          {{ s.label }}
        </span>
      </div>
      <div v-if="i < steps.length - 1" 
           style="flex: 1; height: 2px; margin: 0 12px; margin-top: -20px; transition: background 0.3s;"
           :style="{ background: step > s.num ? '#002a4d' : '#e0e3e5' }">
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  step: {
    type: Number,
    required: true
  }
});

const steps = [
  { num: 1, label: "البيانات الشخصية" },
  { num: 2, label: "التحقق من الهاتف" },
  { num: 3, label: "تأكيد التسجيل" },
];
</script>
