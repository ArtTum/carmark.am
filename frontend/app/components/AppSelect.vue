<script setup lang="ts">
type SelectValue = string | number;
type SelectOption = {
  label: string;
  value: SelectValue;
};

const props = withDefaults(defineProps<{
  modelValue?: SelectValue;
  options: Array<SelectOption | SelectValue>;
  placeholder?: string;
  searchable?: boolean;
  searchPlaceholder?: string;
  disabled?: boolean;
}>(), {
  modelValue: '',
  placeholder: 'Ընտրել',
  searchPlaceholder: 'Որոնել',
});

const emit = defineEmits<{
  'update:modelValue': [value: SelectValue];
  change: [value: SelectValue];
}>();

const root = ref<HTMLElement | null>(null);
const isOpen = ref(false);
const query = ref('');

const normalizedOptions = computed<SelectOption[]>(() => props.options.map((option) => {
  if (typeof option === 'object' && option !== null && 'value' in option) {
    return option as SelectOption;
  }

  return {
    label: String(option),
    value: option,
  };
}));

const selectedOption = computed(() => normalizedOptions.value.find((option) => sameValue(option.value, props.modelValue)));

const filteredOptions = computed(() => {
  const term = query.value.trim().toLowerCase();
  if (!term) return normalizedOptions.value;
  return normalizedOptions.value.filter((option) => option.label.toLowerCase().includes(term));
});

function sameValue(a?: SelectValue, b?: SelectValue) {
  return String(a ?? '') === String(b ?? '');
}

function toggle() {
  if (props.disabled) return;
  isOpen.value = !isOpen.value;
  if (isOpen.value) query.value = '';
}

function close() {
  isOpen.value = false;
  query.value = '';
}

function choose(value: SelectValue) {
  emit('update:modelValue', value);
  emit('change', value);
  close();
}

function onKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') {
    close();
  }
}

function onDocumentPointerdown(event: PointerEvent) {
  if (!root.value?.contains(event.target as Node)) close();
}

onMounted(() => {
  document.addEventListener('pointerdown', onDocumentPointerdown);
});

onBeforeUnmount(() => {
  document.removeEventListener('pointerdown', onDocumentPointerdown);
});
</script>

<template>
  <div ref="root" class="app-select" :class="{ open: isOpen, disabled }" @keydown="onKeydown">
    <button class="app-select-trigger" type="button" :disabled="disabled" @click="toggle">
      <span :class="{ placeholder: !selectedOption }">{{ selectedOption?.label || placeholder }}</span>
      <svg width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
        <path d="M0.833984 0.833252L5.83398 5.83325L10.834 0.833252" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>

    <div v-if="isOpen" class="app-select-popover">
      <div v-if="searchable" class="app-select-search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <input v-model="query" type="search" :placeholder="searchPlaceholder">
      </div>

      <div class="app-select-options">
        <button
          v-for="option in filteredOptions"
          :key="`${option.value}`"
          class="app-select-option"
          :class="{ active: sameValue(option.value, modelValue) }"
          type="button"
          @click="choose(option.value)"
        >
          <span>{{ option.label }}</span>
          <span class="app-select-check" aria-hidden="true" />
        </button>
        <p v-if="!filteredOptions.length" class="app-select-empty">Արդյունք չկա</p>
      </div>
    </div>
  </div>
</template>
