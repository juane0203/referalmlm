// PRIMERA LÍNEA DEL ARCHIVO:
console.log('WRS_PROFILE_DEBUG: registration-dynamics.js - Archivo cargado en esta página.');

jQuery(document).ready(function($) {
    console.log('WRS_PROFILE_DEBUG: Document ready. Intentando inicializar para "Mi Perfil" (o registro).');
    console.log('WRS_PROFILE_DEBUG: wrs_ajax_object disponible:', typeof wrs_ajax_object !== 'undefined' ? wrs_ajax_object : 'NO DEFINIDO');

    // Usaremos selectores que puedan aplicar a ambos formularios si los IDs son los mismos
    var departamentoSelect = $('#reg_wrs_departamento_viv'); 
    var municipioSelect = $('#reg_wrs_municipio_viv');
    var localidadField = $('.wrs-localidad-field'); 
    var localidadSelect = $('#reg_wrs_localidad'); 
    var barrioFieldContainer = $('#reg_wrs_barrio').closest('.form-row');

    if (departamentoSelect.length > 0) {
        console.log('WRS_PROFILE_DEBUG: Selector de Departamento ENCONTRADO. ID: ' + departamentoSelect.attr('id'));
        
        departamentoSelect.off('change.wrs').on('change.wrs', function() { // Usar namespace para el evento
            console.log('WRS_PROFILE_DEBUG: Evento CHANGE en departamento "' + $(this).attr('id') + '" disparado.');
            var selectedDepartamento = $(this).val();
            console.log('WRS_PROFILE_DEBUG: Departamento seleccionado:', selectedDepartamento);

            municipioSelect.empty().append('<option value="">' + 'Cargando...' + '</option>').prop('disabled', true);
            
            if (localidadField.is(':visible')) {
                localidadField.hide();
                localidadSelect.val(''); 
                console.log('WRS_PROFILE_DEBUG: Campo Localidad ocultado.');
            }
            barrioFieldContainer.removeClass('form-row-last').addClass('form-row-wide');

            if (selectedDepartamento === '') {
                municipioSelect.empty().append('<option value="">' + 'Selecciona un departamento primero...' + '</option>').prop('disabled', true);
                console.log('WRS_PROFILE_DEBUG: Departamento vacío.');
                return;
            }

            console.log('WRS_PROFILE_DEBUG: Haciendo llamada AJAX para departamento:', selectedDepartamento);
            if (typeof wrs_ajax_object === 'undefined' || !wrs_ajax_object.ajax_url) {
                console.error('WRS_PROFILE_DEBUG: wrs_ajax_object o ajax_url no está definido. AJAX no se puede realizar.');
                municipioSelect.empty().append('<option value="">' + 'Error de configuración AJAX' + '</option>').prop('disabled', false);
                return;
            }

            $.ajax({
                url: wrs_ajax_object.ajax_url,
                type: 'POST',
                data: { 
                    action: 'wrs_get_municipios', 
                    departamento: selectedDepartamento
                },
                dataType: 'json',
                success: function(response) {
                    console.log('WRS_PROFILE_DEBUG: AJAX Success. Respuesta:', response);
                    municipioSelect.prop('disabled', false).empty();

                    if (response.success && response.data && Object.keys(response.data).length > 0) {
                        municipioSelect.append('<option value="">' + 'Selecciona un Municipio...' + '</option>');
                        $.each(response.data, function(valueKey, displayText) {
                            municipioSelect.append($('<option>', { value: valueKey, text: displayText }));
                        });
                        console.log('WRS_PROFILE_DEBUG: Municipios poblados.');

                        if (selectedDepartamento === 'BOGOTA_DC') {
                            console.log('WRS_PROFILE_DEBUG: BOGOTA_DC seleccionado.');
                            if (municipioSelect.find('option[value="BOGOTA_DC"]').length > 0) {
                                municipioSelect.val('BOGOTA_DC');
                            } else {
                                municipioSelect.append($('<option>', { value: 'BOGOTA_DC', text: 'Bogot\u00e1 D.C.' })).val('BOGOTA_DC');
                            }
                            localidadField.show();
                            barrioFieldContainer.removeClass('form-row-wide').addClass('form-row-last');
                        } else {
                            // La lógica para ocultar localidad y ajustar barrio ya está arriba.
                        }
                    } else {
                        console.log('WRS_PROFILE_DEBUG: No se encontraron municipios o respuesta no exitosa.');
                        municipioSelect.append('<option value="">' + (response.data && response.data.message ? response.data.message : 'No se encontraron municipios') + '</option>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("WRS_PROFILE_DEBUG: AJAX Error!", { status: textStatus, error: errorThrown, responseText: jqXHR.responseText });
                    municipioSelect.prop('disabled', false).empty().append('<option value="">' + 'Error al cargar' + '</option>');
                }
            });
        }); // Fin de .on('change')

        // Trigger inicial si ya hay un valor
        if (departamentoSelect.val() && departamentoSelect.val() !== '') {
            console.log('WRS_PROFILE_DEBUG: Disparando CHANGE inicial para departamento:', departamentoSelect.val());
            setTimeout(function(){
                departamentoSelect.trigger('change.wrs'); // Disparar evento con namespace
            }, 300); // Aumentar un poco el delay
        } else {
            console.log('WRS_PROFILE_DEBUG: Sin departamento preseleccionado al cargar el formulario de perfil.');
            localidadField.hide(); // Asegurar que localidad esté oculta si no hay depto
            barrioFieldContainer.removeClass('form-row-last').addClass('form-row-wide');
            municipioSelect.empty().append('<option value="">' + 'Selecciona un departamento primero...' + '</option>').prop('disabled', true);
        }
        console.log('WRS_PROFILE_DEBUG: Evento CHANGE adjuntado a departamento.');

    } else {
        console.error('WRS_PROFILE_DEBUG: Selector de departamento #reg_wrs_departamento_viv NO ENCONTRADO en esta página.');
    }
});