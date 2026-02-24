@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Inventario Emdell')

@section('page-title', 'Panel de Control')

@section('styles')
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .stat-card-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark) !important;
            display: block;
        }

        .stat-card-label {
            font-size: 0.9rem;
            color: var(--text-muted) !important;
            font-weight: 500;
        }

        .card-emdell {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
        }

        /* ── TABLAS ── */
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 24px;
            margin-top: 32px;
        }

        .table-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .table-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border-color);
        }

        .table-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .table-card-icon.red  { background: linear-gradient(135deg, var(--emdell-red), #ff4444); }
        .table-card-icon.blue { background: linear-gradient(135deg, #4A90E2, #357ABD); }

        .table-card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark) !important;
            margin: 0;
        }

        .emdell-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .emdell-table thead { background: var(--table-header-bg, rgba(0,0,0,0.03)); }
        [data-theme="dark"] .emdell-table thead { background: rgba(255,255,255,0.05); }

        .emdell-table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted) !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
        }

        .emdell-table td {
            padding: 14px 16px;
            color: var(--text-dark) !important;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        .emdell-table tbody tr { transition: background-color 0.2s ease; }
        .emdell-table tbody tr:hover { background: var(--hover-bg, rgba(0,0,0,0.02)); }
        [data-theme="dark"] .emdell-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        .emdell-table tbody tr:last-child td { border-bottom: none; }

        /* Badges de stock */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .stock-badge.critical { background: rgba(239,68,68,0.1);   color: #ef4444; }
        .stock-badge.low      { background: rgba(251,146,60,0.1);   color: #fb923c; }

        /* Badges de movimiento */
        .movement-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .movement-badge.entrada { background: rgba(34,197,94,0.1);  color: #22c55e; }
        .movement-badge.salida  { background: rgba(239,68,68,0.1);  color: #ef4444; }

        .quantity-indicator         { font-weight: 700; font-size: 1rem; }
        .quantity-indicator.positive { color: #22c55e; }
        .quantity-indicator.negative { color: #ef4444; }

        .timestamp {
            font-size: 0.8rem;
            color: var(--text-muted) !important;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .no-data-message { text-align: center; padding: 40px 20px; color: var(--text-muted) !important; }
        .no-data-icon    { font-size: 3rem; opacity: 0.3; margin-bottom: 12px; }

        @media (max-width: 1200px) { .tables-grid { grid-template-columns: 1fr; } }

        @media (max-width: 768px) {
            .emdell-table { font-size: 0.85rem; }
            .emdell-table th, .emdell-table td { padding: 10px 12px; }
            .table-card { padding: 16px; }
        }
    </style>
@endsection

@section('content')

    {{-- ── TARJETAS DE ESTADÍSTICAS ── --}}
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-card-icon red mb-2" style="color: var(--emdell-red); font-size: 1.5rem;">
                <i class="bi bi-box-seam-fill" style="color: white"></i>
            </div>
            <div class="stat-card-value">{{ number_format($totalMateriales) }}</div>
            <div class="stat-card-label">Total Materiales</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon orange mb-2" style="color: var(--emdell-orange); font-size: 1.5rem;">
                <i class="bi bi-arrow-left-right" style="color: white"></i>
            </div>
            <div class="stat-card-value">{{ number_format($movimientosMes) }}</div>
            <div class="stat-card-label">Movimientos del Mes</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon yellow mb-2" style="color: var(--emdell-yellow); font-size: 1.5rem;">
                <i class="bi bi-exclamation-triangle-fill" style="color: white"></i>
            </div>
            <div class="stat-card-value">{{ number_format($totalBajoStock) }}</div>
            <div class="stat-card-label">Materiales Bajo Stock</div>
        </div>
    </div>

    {{-- ── TABLAS ── --}}
    <div class="tables-grid">

        {{-- Tabla de Materiales con Bajo Stock --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-icon red">
                    <i class="bi bi-exclamation-triangle-fill" style="color: white;"></i>
                </div>
                <h3 class="table-card-title">Materiales de Bajo Stock</h3>
            </div>

            <div class="table-responsive">
                <table class="emdell-table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Stock Actual</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materialesBajoStock as $material)
                            @php
                                $detalle   = $material->detalleMaterial;
                                $critico   = $detalle && $detalle->cantidad_actual <= 0;
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $material->nombre }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $material->codigo }}</div>
                                </td>
                                <td>
                                    <span class="quantity-indicator negative">
                                        {{ $detalle ? number_format($detalle->cantidad_actual, 2) : '—' }}
                                        {{ $material->unidadMedida->abreviatura ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    @if($critico)
                                        <span class="stock-badge critical">
                                            <i class="bi bi-exclamation-circle-fill"></i> Crítico
                                        </span>
                                    @else
                                        <span class="stock-badge low">
                                            <i class="bi bi-dash-circle-fill"></i> Bajo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="no-data-message">
                                        <div class="no-data-icon"><i class="bi bi-check-circle-fill"></i></div>
                                        <div>No hay materiales con stock bajo</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabla de Movimientos Recientes --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-icon blue">
                    <i class="bi bi-clock-history" style="color: white;"></i>
                </div>
                <h3 class="table-card-title">Movimientos Recientes</h3>
            </div>

            <div class="table-responsive">
                <table class="emdell-table">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientosRecientes as $mov)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $mov->material->nombre }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $mov->material->codigo }}</div>
                                </td>
                                <td>
                                    @if($mov->tipo_movimiento === 'entrada')
                                        <span class="movement-badge entrada">
                                            <i class="bi bi-arrow-down-circle-fill"></i> Entrada
                                        </span>
                                    @else
                                        <span class="movement-badge salida">
                                            <i class="bi bi-arrow-up-circle-fill"></i> Salida
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($mov->tipo_movimiento === 'entrada')
                                        <span class="quantity-indicator positive">+{{ number_format($mov->cantidad, 2) }}</span>
                                    @else
                                        <span class="quantity-indicator negative">-{{ number_format($mov->cantidad, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="timestamp">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $mov->fecha->format('d/m/Y') }}
                                    </div>
                                    <div class="timestamp mt-1">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $mov->created_at->setTimezone('America/La_Paz')->format('H:i:s') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="no-data-message">
                                        <div class="no-data-icon"><i class="bi bi-inbox"></i></div>
                                        <div>No hay movimientos recientes</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection