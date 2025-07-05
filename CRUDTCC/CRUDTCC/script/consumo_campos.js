// script para exibir/ocultar campos de consumo conforme o tipo de combustível
window.addEventListener('DOMContentLoaded', function() {
    var combusSelect = document.querySelector('select[name="CarCombus"]');
    var consumo2Input = document.getElementById('consumo2');
    var consumo3Group = document.getElementById('consumo3-group');
    var consumoGNVGroup = document.getElementById('consumoGNV-group');
    var consumoLabel = document.getElementById('consumo-label');
    function toggleConsumoFields() {
        if (!combusSelect) return;
        var tipo = combusSelect.value;
        var consumoGroup = document.getElementById('consumo-group');
        var consumo2Group = consumo2Input ? consumo2Input.closest('.col-md-6.mb-3') : null;
        // Consumo2: Flex ou Híbrido
        if (tipo === 'Flex' || tipo === 'Híbrido') {
            if (consumo2Group) consumo2Group.style.display = '';
            if (consumoLabel) consumoLabel.textContent = 'Consumo Gasolina (km/l):';
        } else {
            if (consumo2Group) consumo2Group.style.display = 'none';
            if (consumo2Input) consumo2Input.value = '';
            if (consumoLabel) consumoLabel.textContent = 'Consumo (km/l):';
        }
        // Consumo3: Híbrido ou Elétrico
        if (tipo === 'Híbrido' || tipo === 'Elétrico') {
            if (consumo3Group) consumo3Group.style.display = '';
        } else {
            if (consumo3Group) consumo3Group.style.display = 'none';
            var consumo3Input = document.getElementById('consumo3');
            if (consumo3Input) consumo3Input.value = '';
        }
        // Consumo GNV: apenas para GNV
        if (tipo === 'GNV') {
            if (consumoGNVGroup) consumoGNVGroup.style.display = '';
            if (consumoGroup) consumoGroup.style.display = 'none';
        } else {
            if (consumoGNVGroup) consumoGNVGroup.style.display = 'none';
            var consumoGNVInput = document.getElementById('consumoGNV');
            if (consumoGNVInput) consumoGNVInput.value = '';
            if (consumoGroup) consumoGroup.style.display = '';
        }
    }
    if (combusSelect) {
        combusSelect.addEventListener('change', toggleConsumoFields);
        toggleConsumoFields();
    }
});
