class DashboardChart {
  constructor(data, handleLeave, handleHover) {
    // Cache canvas contexts
    this.ctx = this.getCanvasContext('myChart');
    this.ctx2 = this.getCanvasContext('myChart2');
    this.ctx3 = this.getCanvasContext('myChart3');

    // Validate contexts
    if (!this.ctx || !this.ctx2 || !this.ctx3) {
      throw new Error('One or more canvas elements not found');
    }

    // Store event handlers
    this.handleLeave = handleLeave;
    this.handleHover = handleHover;

    // Validate input data
    if (!data || typeof data !== 'object') {
      throw new Error('Invalid data provided');
    }

    // Initialize charts
    this.createDoughnutChart(data);
    this.createDataSetCharts(data);
  }

  // Helper to get canvas context with validation
  getCanvasContext(id) {
    const canvas = document.getElementById(id);
    return canvas ? canvas.getContext('2d') : null;
  }

  // Generate random HSLA color with constrained alpha
  getRandomColorHSLA() {
    const getRandomValue = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
    const hue = getRandomValue(0, 360);
    const saturation = getRandomValue(50, 100); // Improved contrast
    const lightness = getRandomValue(40, 80); // Avoid too dark/light
    const alpha = Math.min(1, Math.random() * 0.6 + 0.4); // Alpha between 0.4 and 1
    return `hsla(${hue}, ${saturation}%, ${lightness}%, ${alpha})`;
  }

  // Create a dataset for charts
  createDataset(label, data, backgroundColorCount) {
    return {
      label,
      data,
      borderWidth: 1,
      backgroundColor: Array.from({ length: backgroundColorCount }, () => this.getRandomColorHSLA()),
    };
  }

  // Common chart creation logic
  createChart(ctx, type, data, options) {
    return new Chart(ctx, {
      type,
      data,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
            onHover: this.handleHover,
            onLeave: this.handleLeave,
          },
          title: {
            display: true,
            ...options.title,
          },
        },
        ...options.chartSpecific,
      },
    });
  }

  // Create dataset for pie and bar charts
  createDataSetCharts(data) {
    const datasets = [];
    const datasets2 = [];

    for (const [key, value] of Object.entries(data)) {
      if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
        for (const subValue of Object.values(value)) {
          // Bar chart dataset (TableRows)
          datasets2.push(this.createDataset(key, [subValue?.table_rows || 0], 1));

          // Pie chart dataset (AvgRowLength, DataLength, IndexLength)
          datasets.push(
            this.createDataset(
              key,
              [
                subValue?.avg_row_length?.kb || 0,
                subValue?.data_length?.kb || 0,
                subValue?.index_length?.kb || 0,
              ],
              3
            )
          );
        }
      }
    }

    // Create Pie chart
    this.createChart(this.ctx2, 'doughnut', {
      labels: ['AvgRowLength', 'DataLength', 'IndexLength'],
      datasets,
    }, {
      title: { text: 'Data Tables' },
    });

    // Create Bar chart
    this.createChart(this.ctx3, 'bar', {
      labels: ['TableRows'],
      datasets: datasets2,
    }, {
      title: { text: 'Rows Tables' },
      chartSpecific: { indexAxis: 'y' },
    });
  }

  // Create doughnut chart for total server capacity
  createDoughnutChart(data) {
    if (!data?.total) {
      console.warn('No total data provided for doughnut chart');
      return;
    }

    const totalData = data.total;
    const chartData = {
      labels: ['total_avg_row_length', 'total_data_length', 'total_index_length'],
      datasets: [{
        label: 'Tamaño (KB)',
        data: [
          totalData.total_avg_row_length?.kb || 0,
          totalData.total_data_length?.kb || 0,
          totalData.total_index_length?.kb || 0,
        ],
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(75, 192, 192, 0.2)',
        ],
        borderColor: [
          'rgba(255, 99, 132, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(75, 192, 192, 1)',
        ],
        borderWidth: 1,
      }],
    };

    this.createChart(this.ctx, 'doughnut', chartData, {
      title: { text: 'Server Capacity' },
    });
  }
}