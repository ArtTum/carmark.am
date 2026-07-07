<script setup lang="ts">
type SelectValue = string | number;
type AuctionName = 'IAAI' | 'Copart';
type PdfVariant = 'simplified' | 'individual';

const calculated = ref(false);
const pdfPending = ref<PdfVariant | ''>('');
const pdfError = ref('');

const form = reactive({
  price: 1000,
  engine: 'gasoline',
  auction: 'IAAI' as AuctionName,
  auctionFee: 480,
  age: 'under_3',
  year: 2025,
  auctionLocation: 'AL-DOTHAN',
  engineSize: 1200,
  transportFee: 2440,
  body: 'sedan',
  insurance: true,
});

const auctionDefaults: Record<AuctionName, number> = {
  IAAI: 480,
  Copart: 520,
};

const auctionLocations = [
  'AL-DOTHAN',
  'CA-LOS ANGELES',
  'GA-ATLANTA EAST',
  'TX-HOUSTON',
  'NJ-SOMERVILLE',
  'FL-MIAMI NORTH',
];

const engineOptions = [
  { label: 'Բենզին', value: 'gasoline' },
  { label: 'Դիզել', value: 'diesel' },
  { label: 'Հիբրիդ', value: 'hybrid' },
  { label: 'Էլեկտրական', value: 'electric' },
];

const ageOptions = [
  { label: 'մինչև 3 տարի', value: 'under_3' },
  { label: '3-5 տարի', value: 'three_to_five' },
  { label: '5-7 տարի', value: 'five_to_seven' },
  { label: '7 տարուց ավել', value: 'over_seven' },
];

const bodyOptions = [
  { label: 'Սեդան', value: 'sedan' },
  { label: 'Ամենագնաց', value: 'suv' },
  { label: 'Հեթչբեք', value: 'hatchback' },
  { label: 'Պիկապ', value: 'pickup' },
  { label: 'Կուպե', value: 'coupe' },
];

const yearOptions = Array.from({ length: 18 }, (_, index) => 2026 - index);

const selectedEngine = computed(() => optionLabel(engineOptions, form.engine));
const selectedAge = computed(() => optionLabel(ageOptions, form.age));
const selectedBody = computed(() => optionLabel(bodyOptions, form.body));
const carPrice = computed(() => positiveNumber(form.price));
const auctionFee = computed(() => positiveNumber(form.auctionFee));
const transportFee = computed(() => positiveNumber(form.transportFee));
const engineSize = computed(() => positiveNumber(form.engineSize));
const serviceFee = computed(() => Math.max(300, Math.round(carPrice.value * 0.03)));
const insuranceFee = computed(() => (form.insurance ? Math.max(39, Math.round(carPrice.value * 0.039)) : 0));

const baseRows = computed(() => [
  { label: 'Վճարման հայտը աճուրդում', value: carPrice.value },
  { label: 'Աճուրդի միջնորդավճար', value: auctionFee.value },
  { label: 'Ծառայության վճար', value: serviceFee.value },
  { label: 'Տեղափոխում (ԱՄՆ, Գյումրի)', value: transportFee.value },
]);

const subtotal = computed(() => (
  carPrice.value
  + auctionFee.value
  + serviceFee.value
  + transportFee.value
  + insuranceFee.value
));

const ageRates: Record<string, number> = {
  under_3: 0.495,
  three_to_five: 0.58,
  five_to_seven: 0.68,
  over_seven: 0.82,
};

const simplifiedRows = computed(() => {
  const duty = form.engine === 'electric' ? 0 : Math.round(engineSize.value * (ageRates[form.age] || ageRates.under_3));
  const vat = Math.round((subtotal.value + duty) * 0.1878);
  const eco = form.engine === 'electric' ? 0 : Math.round(engineSize.value * 0.066);
  const clearance = 75;

  return [
    { label: 'Մաքսատուրք', value: duty },
    { label: 'ԱԱՀ', value: vat },
    { label: 'Բնապահպանական հարկ', value: eco },
    { label: 'Բրոքերային ծառայություն', value: clearance },
  ];
});

const individualRows = computed(() => {
  const customs = form.engine === 'electric' ? Math.round(engineSize.value * 0.72) : Math.round(engineSize.value * 2.8425);
  const ecology = form.engine === 'electric' ? 0 : 30;

  return [
    { label: 'Մաքսային ծառայություն', value: customs },
    { label: 'Բնապահպանական հարկ', value: ecology },
  ];
});

const simplifiedCustomsTotal = computed(() => sumRows(simplifiedRows.value));
const individualCustomsTotal = computed(() => sumRows(individualRows.value));
const simplifiedTotal = computed(() => subtotal.value + simplifiedCustomsTotal.value);
const individualTotal = computed(() => subtotal.value + individualCustomsTotal.value);

const pdfVariants = computed(() => ({
  simplified: {
    title: 'Իրավաբանական անձ',
    fileName: 'carmark-legal-customs-calculation.pdf',
    rows: simplifiedRows.value,
    total: simplifiedTotal.value,
  },
  individual: {
    title: 'Ֆիզիկական անձ',
    fileName: 'carmark-individual-customs-calculation.pdf',
    rows: individualRows.value,
    total: individualTotal.value,
  },
}));

function positiveNumber(value: number | string) {
  const parsed = Number(value);
  return Number.isFinite(parsed) && parsed > 0 ? parsed : 0;
}

function sumRows(rows: Array<{ value: number }>) {
  return rows.reduce((total, row) => total + Number(row.value || 0), 0);
}

function optionLabel(options: Array<{ label: string; value: SelectValue }>, value: SelectValue) {
  return options.find((option) => String(option.value) === String(value))?.label || String(value || '');
}

function selectAuction(auction: AuctionName) {
  form.auction = auction;
  form.auctionFee = auctionDefaults[auction];
}

function calculate() {
  pdfError.value = '';
  calculated.value = true;
}

function safeText(value: string) {
  return value.replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }[char] || char));
}

function renderPdfRows(rows: Array<{ label: string; value: number }>) {
  return rows
    .map((row) => `<div><span>${safeText(row.label)}</span><strong>${money(row.value)}</strong></div>`)
    .join('');
}

async function downloadPdf(variant: PdfVariant) {
  if (!import.meta.client) return;

  pdfPending.value = variant;
  pdfError.value = '';

  try {
    const module = await import('html2pdf.js') as any;
    const html2pdf = module.default || module;
    const data = pdfVariants.value[variant];
    const mount = document.createElement('div');
    mount.className = 'calculator-pdf-render-root';
    mount.innerHTML = `
      <section class="calculator-pdf-document">
        <header>
          <strong>carmark</strong>
          <span>Մաքսային հաշվարկ</span>
        </header>
        <h1>${safeText(data.title)}</h1>
        <div class="calculator-pdf-meta">
          <div><span>Աճուրդ</span><strong>${safeText(form.auction)}</strong></div>
          <div><span>Աճուրդի վայր</span><strong>${safeText(form.auctionLocation)}</strong></div>
          <div><span>Շարժիչ</span><strong>${safeText(selectedEngine.value)}</strong></div>
          <div><span>Տարի</span><strong>${safeText(String(form.year))}</strong></div>
          <div><span>Թափք</span><strong>${safeText(selectedBody.value)}</strong></div>
          <div><span>Շարժիչի ծավալ</span><strong>${safeText(String(form.engineSize))} սմ³</strong></div>
        </div>
        <h2>Ընդհանուր ծախսեր</h2>
        <div class="calculator-pdf-rows">
          ${renderPdfRows(baseRows.value)}
          <div><span>Լիարժեք ապահովագրում</span><strong>${money(insuranceFee.value)}</strong></div>
          <div class="total"><span>Ընդհանուր գին առանց մաքսազերծման</span><strong>${money(subtotal.value)}</strong></div>
        </div>
        <h2>${safeText(data.title)} վճարներ</h2>
        <div class="calculator-pdf-rows">
          ${renderPdfRows(data.rows)}
          <div class="total"><span>Ընդհանուր գին</span><strong>${money(data.total)}</strong></div>
        </div>
      </section>
    `;

    document.body.appendChild(mount);
    await html2pdf()
      .set({
        filename: data.fileName,
        margin: 0,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, backgroundColor: '#ffffff', useCORS: true },
        jsPDF: { unit: 'px', format: [820, 1120], orientation: 'portrait' },
      })
      .from(mount.firstElementChild)
      .save();
    mount.remove();
  } catch {
    pdfError.value = 'PDF-ը ներբեռնել չհաջողվեց։ Փորձեք կրկին։';
  } finally {
    pdfPending.value = '';
  }
}
</script>

<template>
  <main class="calculator-page">
    <section class="calculator-workspace">
      <form class="calculator-card calculator-pro-card" @submit.prevent="calculate">
        <div class="calculator-field">
          <span>Մեքենայի արժեքը</span>
          <input v-model.number="form.price" type="number" min="0" inputmode="numeric">
        </div>

        <div class="calculator-field">
          <span>Շարժիչի տեսակ</span>
          <AppSelect v-model="form.engine" :options="engineOptions" />
        </div>

        <div class="calculator-field calculator-auction-field">
          <span>Աճուրդ</span>
          <div class="calculator-auction-row">
            <button
              v-for="auction in (['IAAI', 'Copart'] as AuctionName[])"
              :key="auction"
              class="calculator-auction-choice"
              :class="{ active: form.auction === auction, copart: auction === 'Copart' }"
              type="button"
              @click="selectAuction(auction)"
            >
              <span v-if="auction === 'IAAI'" class="iaai-logo">IA<span>A</span></span>
              <span v-else class="copart-logo">Copart</span>
            </button>
            <input v-model.number="form.auctionFee" type="number" min="0" inputmode="numeric" aria-label="Աճուրդի միջնորդավճար">
          </div>
        </div>

        <div class="calculator-split">
          <div class="calculator-field">
            <span>Տարիք</span>
            <AppSelect v-model="form.age" :options="ageOptions" />
          </div>
          <div class="calculator-field">
            <span>Արտադրության տարի</span>
            <AppSelect v-model="form.year" :options="yearOptions" />
          </div>
        </div>

        <div class="calculator-field">
          <span>Աճուրդի վայր</span>
          <AppSelect v-model="form.auctionLocation" :options="auctionLocations" searchable search-placeholder="Որոնել" />
        </div>

        <div class="calculator-field">
          <span>Շարժիչի ծավալ, սմ³</span>
          <input v-model.number="form.engineSize" type="number" min="0" inputmode="numeric">
        </div>

        <div class="calculator-field">
          <span>Տեղափոխման վճար</span>
          <input v-model.number="form.transportFee" type="number" min="0" inputmode="numeric">
        </div>

        <div class="calculator-field">
          <span>Տրանսպորտի տեսակ</span>
          <AppSelect v-model="form.body" :options="bodyOptions" />
        </div>

        <button class="calculator-main-submit" type="submit">Հաշվել</button>

        <Transition name="calculator-results">
          <section v-if="calculated" class="calculator-results-panel">
            <div class="calculator-summary">
              <div v-for="row in baseRows" :key="row.label" class="calculator-summary-row">
                <span>{{ row.label }}</span>
                <strong>{{ money(row.value) }}</strong>
              </div>
              <label class="calculator-insurance-row">
                <span>
                  <input v-model="form.insurance" type="checkbox">
                  <i aria-hidden="true" />
                  Լիարժեք ապահովագրում
                </span>
                <strong>{{ money(insuranceFee) }}</strong>
              </label>
              <div class="calculator-summary-total">
                <span>Ընդհանուր գին առանց մաքսազերծման</span>
                <strong>{{ money(subtotal) }}</strong>
              </div>
            </div>

            <h2>Մաքսային վճարներ</h2>

            <div class="calculator-duty-grid">
              <article class="calculator-duty-card featured">
                <h3>Իրավաբանական անձ</h3>
                <div class="calculator-duty-lines">
                  <div v-for="row in simplifiedRows" :key="row.label">
                    <span>{{ row.label }}</span>
                    <strong>{{ money(row.value) }}</strong>
                  </div>
                </div>
                <div class="calculator-duty-total">
                  <span>Ընդհանուր գին</span>
                  <strong>{{ money(simplifiedTotal) }}</strong>
                </div>
                <button class="calculator-download" type="button" :disabled="pdfPending === 'simplified'" @click="downloadPdf('simplified')">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3V15M12 15L7 10M12 15L17 10M5 19H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  {{ pdfPending === 'simplified' ? 'Պատրաստվում է...' : 'Ներբեռնել' }}
                </button>
              </article>

              <article class="calculator-duty-card">
                <h3>Ֆիզիկական անձ</h3>
                <div class="calculator-duty-lines">
                  <div v-for="row in individualRows" :key="row.label">
                    <span>{{ row.label }}</span>
                    <strong>{{ money(row.value) }}</strong>
                  </div>
                </div>
                <div class="calculator-duty-total">
                  <span>Ընդհանուր գին</span>
                  <strong>{{ money(individualTotal) }}</strong>
                </div>
                <button class="calculator-download" type="button" :disabled="pdfPending === 'individual'" @click="downloadPdf('individual')">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3V15M12 15L7 10M12 15L17 10M5 19H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  {{ pdfPending === 'individual' ? 'Պատրաստվում է...' : 'Ներբեռնել' }}
                </button>
              </article>
            </div>

            <p v-if="pdfError" class="form-error calculator-pdf-error">{{ pdfError }}</p>
          </section>
        </Transition>
      </form>
    </section>
  </main>
</template>
