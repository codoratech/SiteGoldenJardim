(() => {
  const node = document.getElementById('dashboard-data');
  if (!node) return;
  const stats = JSON.parse(node.textContent), charts = [];
  const money = new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'});
  const css = name => getComputedStyle(document.documentElement).getPropertyValue(name).trim();
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const configs = [
    {id:'orders',type:'bar',labels:stats.labels,sets:[{label:'Ordens',data:stats.orders,color:'--primary'}],empty:'Nenhuma ordem nos últimos seis meses. Novas ordens aparecerão aqui.'},
    {id:'status',type:'doughnut',labels:stats.statusLabels,sets:[{label:'Ordens',data:stats.statusValues}],empty:'Ainda não há ordens para distribuir por status.'},
    {id:'finance',type:'bar',labels:stats.labels,sets:[{label:'Recebido',data:stats.received,color:'--primary'},{label:'Pendente',data:stats.pending,color:'--warning'}],currency:true,empty:'Sem contas pagas ou pendentes com vencimento neste período.'},
    {id:'services',type:'bar',horizontal:true,labels:stats.serviceLabels,sets:[{label:'Quantidade realizada',data:stats.serviceValues,color:'--primary'}],empty:'Vincule serviços às ordens concluídas para visualizar os mais realizados.'}
  ];
  const dataTable = config => {
    const parent = document.getElementById('chart-data-'+config.id), table = document.createElement('table');
    const head = table.createTHead().insertRow();
    ['Período / categoria',...config.sets.map(s=>s.label)].forEach(label=>{const th=document.createElement('th');th.scope='col';th.textContent=label;head.append(th);});
    const body=table.createTBody();config.labels.forEach((label,index)=>{const row=body.insertRow();[label,...config.sets.map(s=>config.currency?money.format(s.data[index]||0):s.data[index]||0)].forEach(value=>{row.insertCell().textContent=value;});});
    if (!config.labels.length) { const p=document.createElement('p');p.textContent=config.empty;parent.append(p); } else parent.append(table);
  };
  configs.forEach(dataTable);
  function render() {
    charts.splice(0).forEach(chart=>chart.destroy());
    configs.forEach(config=>{
      const frame=document.querySelector('[data-chart-frame="'+config.id+'"]'), canvas=frame.querySelector('canvas'), empty=frame.querySelector('.chart-empty');
      frame.querySelector('.chart-skeleton').hidden=true;frame.setAttribute('aria-busy','false');
      const noData=!config.sets.some(s=>s.data.some(v=>Number(v)>0));
      canvas.hidden=noData || typeof Chart==='undefined';empty.hidden=!canvas.hidden;
      if (canvas.hidden) { empty.textContent=noData?config.empty:'Não foi possível carregar o gráfico. Consulte os dados em texto abaixo.';return; }
      const isDonut=config.type==='doughnut';
      const datasets=config.sets.map(set=>({label:set.label,data:set.data,backgroundColor:isDonut?[css('--warning'),css('--blue'),css('--primary'),css('--danger'),css('--muted')]:css(set.color),borderColor:isDonut?css('--surface'):css(set.color),borderWidth:isDonut?4:0,borderRadius:isDonut?0:5,maxBarThickness:32}));
      const scales=isDonut?undefined:{x:{grid:{display:!!config.horizontal,color:css('--chart-grid')},ticks:{color:css('--muted'),font:{size:10},precision:0},border:{display:false}},y:{beginAtZero:true,grid:{display:!config.horizontal,color:css('--chart-grid')},border:{display:false},ticks:{color:css('--muted'),font:{size:10},precision:0,callback:config.currency?(value)=>money.format(value):undefined}}};
      charts.push(new Chart(canvas,{type:config.type,data:{labels:config.labels,datasets},options:{responsive:true,maintainAspectRatio:false,indexAxis:config.horizontal?'y':'x',cutout:isDonut?'73%':undefined,animation:reduced?false:{duration:450},scales,plugins:{legend:{display:isDonut||config.sets.length>1,position:'bottom',labels:{color:css('--muted'),usePointStyle:true,pointStyle:'circle',boxWidth:7,padding:18,font:{family:'Inter',size:10}}},tooltip:{backgroundColor:css('--surface-raised'),titleColor:css('--text'),bodyColor:css('--text'),borderColor:css('--border'),borderWidth:1,padding:12,callbacks:config.currency?{label:context=>context.dataset.label+': '+money.format(context.parsed.y)}:undefined}}}}));
    });
  }
  render();document.addEventListener('admin:theme',render);
})();
