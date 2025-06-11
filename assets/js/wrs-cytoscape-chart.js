// wrs-cytoscape-chart.js (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS)
console.log('WRS CYTOSCAPE DEBUG: wrs-cytoscape-chart.js loaded (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS).');

document.addEventListener('DOMContentLoaded', function() {
    console.log('WRS CYTOSCAPE DEBUG: DOMContentLoaded fired (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS).');

    if (typeof window.cytoscape === 'undefined') {
        console.error('WRS CYTOSCAPE DEBUG: Cytoscape core IS UNDEFINED. Cannot proceed.');
        var errDivMTBF = document.getElementById('wrs_admin_chart_div') || document.getElementById('wrs_cytoscape_chart_user_div');
        if (errDivMTBF) errDivMTBF.innerHTML = '<p style="color:red;">Error: Cytoscape lib no cargó.</p>';
        return;
    }
    console.log('WRS CYTOSCAPE DEBUG: Cytoscape core IS defined.');

    // No hay lógica de registro de Dagre aquí

    var chartElements;
    var chartDivId;
    var layoutConfigFromPHP = {};
    var isUserView = false;
    var currentTooltip = null; 

    if (typeof wrsAdminChartData !== 'undefined' && wrsAdminChartData.chartDivId && document.getElementById(wrsAdminChartData.chartDivId)) {
        chartElements = wrsAdminChartData.elements; chartDivId = wrsAdminChartData.chartDivId;
        layoutConfigFromPHP = { 
            name: wrsAdminChartData.layoutName || 'breadthfirst', // Default a breadthfirst
            roots: wrsAdminChartData.rootNodeIds // Importante para breadthfirst
        };
        isUserView = false;
        console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Admin view. Data:', chartElements ? chartElements.length : 0, 'Layout Req:', layoutConfigFromPHP.name, 'Roots:', layoutConfigFromPHP.roots);
    } else if (typeof wrsChartData !== 'undefined' && wrsChartData.chartDivId && document.getElementById(wrsChartData.chartDivId)) {
        chartElements = wrsChartData.elements; chartDivId = wrsChartData.chartDivId;
        layoutConfigFromPHP = { name: wrsChartData.layoutName || 'cose', roots: wrsChartData.rootNodeIds };
        isUserView = true;
        console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): User view. Data:', chartElements ? chartElements.length : 0, 'Layout Req:', layoutConfigFromPHP.name);
    } else {
        console.error('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): No chart data or target DIV.');
        // ... (error UI)
        return;
    }
    
    var cyContainer = document.getElementById(chartDivId);
    if (!cyContainer) { console.error('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Container DIV NOT found for id:', chartDivId); return; }
    if (cyContainer.clientHeight < 300 ) { cyContainer.style.height = '600px'; console.warn('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Container height forced for #' + chartDivId); }
    if (!chartElements || chartElements.length === 0) { console.warn('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): No elements to draw.'); cyContainer.innerHTML = '<p>No hay datos de red para mostrar.</p>'; return; }
    
    let effectiveLayoutOptions = { 
        name: layoutConfigFromPHP.name, 
        fit: true, 
        padding: 30, 
        animate: false, // Puedes ponerlo a true si quieres animación al cargar
        nodeDimensionsIncludeLabels: false 
    };

    if (effectiveLayoutOptions.name === 'breadthfirst') {
        console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Configurando layout Breadthfirst.');
        effectiveLayoutOptions.directed = true;
        effectiveLayoutOptions.spacingFactor = 1.2; // Puedes ajustar esto
        effectiveLayoutOptions.grid = false;
        effectiveLayoutOptions.avoidOverlap = true;
        if (layoutConfigFromPHP.roots && layoutConfigFromPHP.roots.length > 0) {
            effectiveLayoutOptions.roots = layoutConfigFromPHP.roots;
            console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Usando roots para breadthfirst:', layoutConfigFromPHP.roots);
        } else {
            console.warn('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Breadthfirst layout SIN roots definidos, podría no verse óptimo.');
        }
    } else if (effectiveLayoutOptions.name === 'cose') { 
        Object.assign(effectiveLayoutOptions, { idealEdgeLength: 100, nodeOverlap: 10, refresh: 10, gravity: 50, numIter: 800, initialTemp: 150, coolingFactor: 0.95, minTemp: 1.0, nodeRepulsion: 200000, animate: 'end', animationDuration: 600 }); 
    } else if (['grid', 'circle', 'concentric', 'random', 'preset'].indexOf(effectiveLayoutOptions.name) === -1) { 
        console.warn(`WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Layout '${effectiveLayoutOptions.name}' no es un layout base conocido, usando 'grid'.`); 
        effectiveLayoutOptions.name = 'grid';
    }

    console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Final Layout Options:', JSON.parse(JSON.stringify(effectiveLayoutOptions)));
    try {
        var cy = window.cytoscape({ container: cyContainer, elements: chartElements, style: [ /* Tu array de estilos completo */ { selector: 'node', style: { 'shape': 'ellipse', 'background-color': '#BDBDBD', 'width': function(ele) { var s = 40; if (ele.data('isPrincipal')) { var c = ele.data('downlineCount')||0; s=Math.min(50+(c*3.5),90); } return s+'px'; }, 'height': function(ele) { var s = 40; if (ele.data('isPrincipal')) { var c = ele.data('downlineCount')||0; s=Math.min(50+(c*3.5),90); } return s+'px'; }, 'border-width': 1.5, 'border-color': '#FFFFFF', 'label': '' }}, { selector: 'node[?isPrincipal]', style: { 'background-color': '#2ECC71', 'border-color': '#27AE60' } }, { selector: 'node[!isPrincipal][colorType="blue1"]', style: { 'background-color': '#3498DB', 'border-color': '#2980B9' } }, { selector: 'node[!isPrincipal][colorType="blue2"]', style: { 'background-color': '#5DADE2', 'border-color': '#3498DB' } }, { selector: 'node[!isPrincipal][colorType="magenta1"]', style: { 'background-color': '#E91E63', 'border-color': '#C2185B' } }, { selector: 'node[!isPrincipal][colorType="magenta2"]', style: { 'background-color': '#F06292', 'border-color': '#E91E63' } }, { selector: 'edge', style: { 'width': 1, 'line-color': '#D0D3D4', 'target-arrow-shape': 'none', 'curve-style': 'bezier' } }, { selector: 'node.hover-effect', style: { 'border-width': 3, 'border-color': '#333333' }} ], layout: effectiveLayoutOptions });
        console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Cytoscape instance created. Nodes:', cy.nodes().length);
        
        // --- LÓGICA DE TOOLTIPS MANUALES (sin cambios) ---
        console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Configurando tooltips manuales.');
        cy.nodes().on('mouseover', function(event) { var node = event.target; var nodeData = node.data(); var nodeName = nodeData.name || 'N/D'; var nodeEmail = nodeData.email || ''; currentTooltip = document.createElement('div'); currentTooltip.innerHTML = '<strong>' + nodeName + '</strong>' + (nodeEmail ? '<br><small>' + nodeEmail + '</small>' : ''); currentTooltip.classList.add('wrs-manual-tooltip'); currentTooltip.style.position = 'absolute'; currentTooltip.style.backgroundColor = 'white'; currentTooltip.style.border = '1px solid #ccc'; currentTooltip.style.padding = '5px 10px'; currentTooltip.style.borderRadius = '3px'; currentTooltip.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)'; currentTooltip.style.zIndex = '10000'; currentTooltip.style.pointerEvents = 'none'; document.body.appendChild(currentTooltip); var chartContainerRect = cyContainer.getBoundingClientRect(); var renderedPosition = node.renderedPosition(); var tooltipTop = chartContainerRect.top + window.scrollY + renderedPosition.y - currentTooltip.offsetHeight - 10; var tooltipLeft = chartContainerRect.left + window.scrollX + renderedPosition.x - (currentTooltip.offsetWidth / 2); if (tooltipLeft < 0) tooltipLeft = 0; if (tooltipTop < 0) tooltipTop = chartContainerRect.top + window.scrollY + renderedPosition.y + node.renderedHeight() + 10; currentTooltip.style.top = tooltipTop + 'px'; currentTooltip.style.left = tooltipLeft + 'px'; node.addClass('hover-effect'); });
        cy.nodes().on('mouseout', function(event) { var node = event.target; if (currentTooltip) { currentTooltip.remove(); currentTooltip = null; } node.removeClass('hover-effect'); });
        cy.on('pan zoom drag', function() { if (currentTooltip) { currentTooltip.remove(); currentTooltip = null; } });
        
        var resizeTimer; window.addEventListener('resize', function() { clearTimeout(resizeTimer); resizeTimer = setTimeout(function() { if (cy && cy.resize) { cy.resize(); cy.fit(); console.log('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Cytoscape viewport redimensionado y ajustado.'); } }, 250); });
    } catch (e) { console.error('WRS CYTOSCAPE DEBUG (v_MANUAL_TOOLTIPS_BREADTHFIRST_FOCUS): Error initializing Cytoscape:', e); if (cyContainer) { cyContainer.innerHTML = '<p style="color: red;">Error JS: ' + e.message + '</p>'; } }
});