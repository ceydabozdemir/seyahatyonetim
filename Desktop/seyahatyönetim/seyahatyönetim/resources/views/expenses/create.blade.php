@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Yeni Gider Ekle</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('expenses.store') }}" method="POST" id="expenseForm">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Gider Adı</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="expense_type" class="form-label">Gider Türü</label>
                <select name="expense_type" id="expense_type" class="form-control" required>
                    <option value="">Seçiniz</option>
                    <option value="general" {{ old('expense_type') == 'general' ? 'selected' : '' }}>Genel Gider</option>
                    <option value="transport" {{ old('expense_type') == 'transport' ? 'selected' : '' }}>Ulaşım</option>
                </select>
                @error('expense_type')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="amount_container">
                <label for="amount_display" class="form-label">Tutar (TL)</label>
                <input type="number" id="amount_display" class="form-control" value="{{ old('amount') }}" step="0.01">
                <!-- Gerçek tutar alanı (gizli) -->
                <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                @error('amount')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="transport_fields" style="display: none;">
                <label for="kilometers" class="form-label">Kilometre</label>
                <input type="number" name="kilometers" id="kilometers" class="form-control" value="{{ old('kilometers') }}" step="0.01">
                @error('kilometers')
                <div class="text-danger">{{ $message }}</div>
                @enderror

                <label for="transportation_vehicle" class="form-label mt-2">Araç</label>
                <select name="transportation_vehicle" id="transportation_vehicle" class="form-control">
                    <option value="">Seçiniz</option>
                    @foreach ($aracs as $arac)
                        <option value="{{ $arac->ad }}" {{ old('transportation_vehicle') == $arac->ad ? 'selected' : '' }}>
                            {{ $arac->ad }}
                        </option>
                    @endforeach
                </select>
                @error('transportation_vehicle')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" id="submit_button">Kaydet</button>
        </form>
    </div>

    <script>
        // Tüm alanlar için referanslar
        const expenseTypeSelect = document.getElementById('expense_type');
        const transportFields = document.getElementById('transport_fields');
        const amountDisplay = document.getElementById('amount_display');
        const amountInput = document.getElementById('amount');
        const kilometersInput = document.getElementById('kilometers');
        const vehicleSelect = document.getElementById('transportation_vehicle');
        const submitButton = document.getElementById('submit_button');
        const amountContainer = document.getElementById('amount_container');
        const expenseForm = document.getElementById('expenseForm');

        // Görünür tutar alanının değişimi için olay dinleyicisi (sadece genel gider için)
        amountDisplay.addEventListener('input', function() {
            if (expenseTypeSelect.value !== 'transport') {
                amountInput.value = this.value;
            }
        });

        // Gider türü değiştiğinde uygulanan işlemler
        expenseTypeSelect.addEventListener('change', function() {
            if (this.value === 'transport') {
                // Ulaşım seçildiğinde
                transportFields.style.display = 'block';
                amountDisplay.readOnly = true;
                amountDisplay.style.backgroundColor = '#e9ecef'; // Devre dışı görünüm ekle
                amountDisplay.value = '';
                amountInput.value = '';
                calculateTransportAmount();
            } else {
                // Genel gider seçildiğinde
                transportFields.style.display = 'none';
                amountDisplay.readOnly = false;
                amountDisplay.style.backgroundColor = '';
                amountDisplay.value = '';
                amountInput.value = '';
            }
        });

        // Sayfa yüklendiğinde kontrol
        document.addEventListener('DOMContentLoaded', function() {
            // Mevcut gider türüne göre alanları ayarla
            if (expenseTypeSelect.value === 'transport') {
                transportFields.style.display = 'block';
                amountDisplay.readOnly = true;
                amountDisplay.style.backgroundColor = '#e9ecef';
                calculateTransportAmount();
            }

            // Sayfa yüklendikten sonra sabit bir değerler listesi oluştur
            // (kullanıcı tarafından değiştirildiğinde kontrol etmek için)
            window.originalVehicleOptions = Array.from(vehicleSelect.options).map(option => option.value);
        });

        // Kilometre ve araç seçimi değiştiğinde tutar hesaplama
        kilometersInput.addEventListener('input', calculateTransportAmount);
        vehicleSelect.addEventListener('change', calculateTransportAmount);

        // Tutar hesaplama fonksiyonu
        function calculateTransportAmount() {
            if (expenseTypeSelect.value !== 'transport') return;

            const kilometers = parseFloat(kilometersInput.value);
            const vehicleName = vehicleSelect.value;

            if (kilometers && vehicleName) {
                submitButton.disabled = true;
                fetch(`/api/get-vehicle-fuel-consumption?vehicle_name=${encodeURIComponent(vehicleName)}&kilometers=${kilometers}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Araç bilgisi alınamadı.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.calculated_amount) {
                            // Hem görünür hem de gizli alana değeri atama
                            amountDisplay.value = data.calculated_amount.toFixed(2);
                            amountInput.value = data.calculated_amount.toFixed(2);
                        } else {
                            throw new Error('Tutar hesaplanamadı.');
                        }
                    })
                    .catch(error => {
                        alert('Hata: ' + error.message);
                        amountDisplay.value = '';
                        amountInput.value = '';
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                    });
            } else {
                amountDisplay.value = '';
                amountInput.value = '';
            }
        }

        // Form gönderilmeden önce son kontroller
        expenseForm.addEventListener('submit', function(event) {
            // Form gönderilmeden önce tüm gizli alanların doğru değerlere sahip olduğundan emin ol
            if (expenseTypeSelect.value === 'transport') {
                // Ulaşım seçildiğinde gerekli kontroller
                const kilometers = parseFloat(kilometersInput.value);
                const vehicleName = vehicleSelect.value;

                // Gerekli değerler boşsa formu engelle
                if (!kilometers || !vehicleName) {
                    event.preventDefault();
                    alert('Lütfen kilometre ve araç bilgilerini eksiksiz giriniz.');
                    return;
                }

                // Tutar hesaplanmamışsa formu engelle
                if (!amountInput.value) {
                    event.preventDefault();
                    alert('Tutar hesaplanamamış. Lütfen kilometre ve araç bilgilerini kontrol ediniz.');
                    return;
                }

                // Tutar uyumsuzluk kontrolü (ekstra güvenlik)
                const currentAmount = amountDisplay.value;
                if (currentAmount !== amountInput.value) {
                    // Değerlerin uyuşmadığı durumu ele al
                    event.preventDefault();
                    // Doğru değeri görünür alana tekrar yaz ve göndermeyi engelle
                    amountDisplay.value = amountInput.value;
                    alert('Tutar değerinde tutarsızlık tespit edildi. Lütfen tekrar deneyiniz.');
                    return;
                }
            } else {
                // Genel gider için tutar kontrolü
                if (!amountDisplay.value || amountDisplay.value <= 0) {
                    event.preventDefault();
                    alert('Lütfen geçerli bir tutar giriniz.');
                    return;
                }

                // Son kez değeri güncelle
                amountInput.value = amountDisplay.value;
            }
        });

        // Kullanıcı sayfadan ayrılmadan önce önemli alanları korumak için
        // devamsız olay dinleyiciler ekleyin
        function preventFieldModification(event) {
            if (expenseTypeSelect.value === 'transport' && amountDisplay.readOnly) {
                event.preventDefault();
                event.stopPropagation();
                // Değeri tekrar ayarla (manipülasyon varsa düzelt)
                calculateTransportAmount();
            }
        }

        // Tutar alanı için koruyucu önlemler
        amountDisplay.addEventListener('change', preventFieldModification);
        amountDisplay.addEventListener('keydown', preventFieldModification);
        amountDisplay.addEventListener('paste', preventFieldModification);
        amountDisplay.addEventListener('drop', preventFieldModification);
    </script>
@endsection
