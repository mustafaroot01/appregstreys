<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="open" class="modal-overlay" @click="$emit('close')">
        <div class="modal-card" @click.stop>
          <!-- Header -->
          <div class="modal-header">
            <div>
              <div class="order-id">#{{ registration.order_id }}</div>
              <h2 class="student-name">{{ registration.full_name }}</h2>
              <div class="meta-row">
                <span>{{ new Date(registration.created_at).toLocaleDateString('ar-IQ') }}</span>
                <span class="dot">•</span>
                <span>{{ registration.gender }}</span>
                <span class="dot">•</span>
                <span
                  class="status-badge"
                  :style="{ background: getStatusStyle(registration.status).bg, color: getStatusStyle(registration.status).color }"
                >
                  <span class="status-dot" :style="{ background: getStatusStyle(registration.status).dot }"></span>
                  {{ getStatusStyle(registration.status).label }}
                </span>
              </div>
            </div>
            <button class="close-btn" @click="$emit('close')">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <!-- Contact -->
            <div class="info-section">
              <div class="section-title">معلومات الاتصال</div>
              <div class="info-grid">
                <div class="info-item">
                  <div class="info-label">رقم الهاتف</div>
                  <div class="info-value ltr">{{ registration.phone }}</div>
                </div>
                <div class="info-item">
                  <div class="info-label">رقم الواتساب</div>
                  <div class="info-value ltr">{{ registration.whatsapp_phone || registration.phone }}</div>
                </div>
                <div class="info-item full">
                  <div class="info-label">عنوان السكن</div>
                  <div class="info-value">{{ registration.address }}</div>
                </div>
                <div v-if="registration.email" class="info-item full">
                  <div class="info-label">البريد الإلكتروني</div>
                  <div class="info-value ltr">{{ registration.email }}</div>
                </div>
              </div>
            </div>

            <div class="divider"></div>

            <!-- Academic -->
            <div class="info-section">
              <div class="section-title">المؤهلات والمسار</div>
              <div class="info-grid">
                <div class="info-item">
                  <div class="info-label">نوع الدراسة</div>
                  <div class="info-value">{{ registration.study_level }}</div>
                </div>
                <div class="info-item">
                  <div class="info-label">نوع التدريب</div>
                  <div class="info-value">{{ registration.training_type || "غير محدد" }}</div>
                </div>
                <div class="info-item">
                  <div class="info-label">الحالة الوظيفية</div>
                  <div class="info-value">{{ registration.is_employee }}</div>
                </div>
                <div class="info-item">
                  <div class="info-label">النقابة</div>
                  <div class="info-value" :class="{ 'text-blue': registration.is_engineer }">
                    {{ registration.is_engineer ? "منتمي" : "غير منتمي" }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Bio -->
            <div v-if="registration.about_me" class="info-section">
              <div class="divider"></div>
              <div class="section-title">نبذة شخصية</div>
              <div class="bio-text">{{ registration.about_me }}</div>
            </div>

            <!-- Engineer ID -->
            <div v-if="registration.is_engineer">
              <div class="divider"></div>
              <div class="info-section">
                <div class="section-title">صورة هوية النقابة</div>
                <div v-if="registration.engineer_id_image_url" class="id-image-wrap">
                  <img :src="registration.engineer_id_image_url" alt="هوية النقابة" class="id-image" />
                </div>
                <div v-else class="id-missing">لم يتم إرفاق صورة الهوية</div>
              </div>
            </div>

            <!-- Actions -->
            <div class="divider"></div>
            <div class="info-section">
              <div class="section-title">الإجراءات</div>
              <div class="form-group">
                <label class="form-label">حالة الطلب</label>
                <select v-model="localStatus" class="form-select">
                  <option value="new">جديد</option>
                  <option value="reviewing">قيد المراجعة</option>
                  <option value="accepted">مقبول</option>
                  <option value="rejected">مرفوض</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">ملاحظات داخلية</label>
                <textarea v-model="localNotes" placeholder="اكتب ملاحظة أو سبب الرفض/القبول..." class="form-textarea"></textarea>
              </div>
            </div>

            <!-- Buttons -->
            <div class="actions-row">
              <button class="btn-delete" @click="onDelete">حذف</button>
              <button class="btn-save" :disabled="saving" @click="onSave">
                {{ saving ? "جاري الحفظ..." : "حفظ التغييرات" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  open: { type: Boolean, required: true },
  registration: { type: Object, default: null },
  saving: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'save', 'delete']);

const localStatus = ref('new');
const localNotes = ref('');

watch(() => props.registration, (reg) => {
  if (reg) {
    localStatus.value = reg.status;
    localNotes.value = reg.admin_notes || '';
  }
}, { immediate: true });

const STATUS_STYLES = {
  new:       { label: "جديد", color: "#1d4ed8", bg: "#eff6ff", dot: "#3b82f6" },
  reviewing: { label: "قيد المراجعة", color: "#b45309", bg: "#fffbeb", dot: "#f59e0b" },
  accepted:  { label: "مقبول", color: "#047857", bg: "#ecfdf5", dot: "#10b981" },
  rejected:  { label: "مرفوض", color: "#b91c1c", bg: "#fef2f2", dot: "#ef4444" },
};

const getStatusStyle = (status) => STATUS_STYLES[status] || STATUS_STYLES.new;

const onSave = () => emit('save', { status: localStatus.value, notes: localNotes.value });
const onDelete = () => emit('delete', props.registration.id);
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 60;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background: rgba(0,0,0,0.4);
  backdrop-filter: blur(4px);
}

.modal-card {
  background: #fff;
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow-y: auto;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.modal-header {
  padding: 24px;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.order-id {
  font-size: 12px;
  color: #94a3b8;
  font-family: monospace;
  letter-spacing: 1px;
  margin-bottom: 4px;
}

.student-name {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.meta-row {
  display: flex;
  gap: 8px;
  align-items: center;
  font-size: 13px;
  color: #64748b;
}

.dot {
  color: #cbd5e1;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.close-btn {
  background: #f8f9fa;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.modal-body {
  padding: 24px;
}

.info-section {
  margin-bottom: 20px;
}

.section-title {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 14px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f1f5f9;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-item.full {
  grid-column: 1 / -1;
}

.info-label {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
}

.info-value {
  font-size: 14px;
  font-weight: 600;
  color: #334155;
}

.info-value.ltr {
  direction: ltr;
  text-align: right;
  font-family: monospace;
}

.text-blue {
  color: #2563eb;
}

.divider {
  height: 1px;
  background: #f1f5f9;
  margin: 20px 0;
}

.bio-text {
  font-size: 14px;
  color: #475569;
  line-height: 1.8;
  background: #f8fafc;
  padding: 16px;
  border-radius: 10px;
}

.id-image-wrap {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 180px;
}

.id-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.id-missing {
  text-align: center;
  padding: 24px;
  color: #94a3b8;
  font-size: 13px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px dashed #e2e8f0;
}

.form-group {
  margin-bottom: 16px;
}

.form-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 6px;
}

.form-select {
  width: 100%;
  padding: 10px 12px;
  font-size: 14px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #fff;
  outline: none;
  font-family: inherit;
  color: #0f172a;
}

.form-select:focus {
  border-color: #002a4d;
}

.form-textarea {
  width: 100%;
  padding: 10px 12px;
  font-size: 13px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #fff;
  outline: none;
  font-family: inherit;
  min-height: 80px;
  resize: vertical;
}

.form-textarea:focus {
  border-color: #002a4d;
}

.actions-row {
  display: flex;
  gap: 10px;
  margin-top: 8px;
}

.btn-save {
  flex: 2;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: #002a4d;
  color: #fff;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: background 0.2s;
  font-family: inherit;
}

.btn-save:hover:not(:disabled) {
  background: #003a6d;
}

.btn-delete {
  flex: 1;
  padding: 12px;
  border-radius: 10px;
  border: 1px solid #fecaca;
  background: #fff;
  color: #dc2626;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}

.btn-delete:hover {
  background: #fef2f2;
}

/* Vue Transition */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-card,
.modal-leave-active .modal-card {
  transition: transform 0.25s ease, opacity 0.25s ease;
}

.modal-enter-from .modal-card,
.modal-leave-to .modal-card {
  transform: scale(0.96) translateY(10px);
  opacity: 0;
}

@media (max-width: 480px) {
  .info-grid {
    grid-template-columns: 1fr;
  }
  .modal-overlay {
    padding: 10px;
  }
}
</style>
