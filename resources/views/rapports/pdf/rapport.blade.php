<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Rapport — DRENA Absences</title>
<style>
body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#333;margin:30px}
h1{font-size:18px;color:#4c1d95;border-bottom:2px solid #7c3aed;padding-bottom:8px;margin-bottom:20px}
h2{font-size:14px;color:#5b21b6;margin-top:25px}
.header{display:flex;justify-content:space-between;border-bottom:1px solid #e5e7eb;padding-bottom:15px;margin-bottom:20px}
.header .logo{font-size:16px;font-weight:bold;color:#4c1d95}
.header .meta{text-align:right;font-size:10px;color:#6b7280}
table{width:100%;border-collapse:collapse;margin:10px 0}
th{background:#f5f3ff;color:#5b21b6;font-size:10px;text-transform:uppercase;letter-spacing:.5px;padding:8px 10px;text-align:left;border-bottom:2px solid #ddd6fe}
td{padding:7px 10px;border-bottom:1px solid #f0ecff;font-size:11px}
tr:nth-child(even){background:#faf9ff}
.stat-box{display:inline-block;width:23%;margin-right:1%;background:#f5f3ff;border-radius:8px;padding:12px;text-align:center;vertical-align:top}
.stat-box .value{font-size:24px;font-weight:bold;color:#4c1d95}
.stat-box .label{font-size:9px;color:#6b7280;text-transform:uppercase;margin-top:4px}
.badge{padding:2px 8px;border-radius:10px;font-size:9px;font-weight:bold}
.badge-green{background:#ecfdf5;color:#059669}.badge-red{background:#fef2f2;color:#dc2626}
.badge-amber{background:#fffbeb;color:#d97706}.badge-blue{background:#eff6ff;color:#2563eb}
.footer{margin-top:30px;border-top:1px solid #e5e7eb;padding-top:10px;font-size:9px;color:#9ca3af;text-align:center}
</style></head>
<body>
<div class="header"><div class="logo">DRENA Absences — MENA</div><div class="meta">Rapport généré le {{ now()->format('d/m/Y à H:i') }}<br>{{ $drenaName ?? 'National' }} — {{ $periode ?? 'Ce mois' }}</div></div>
<h1>Rapport d'Absences</h1>
<div style="margin-bottom:20px">
    <div class="stat-box"><div class="value">{{ $tauxAbsenteisme['total_agents'] }}</div><div class="label">Agents</div></div>
    <div class="stat-box"><div class="value">{{ $tauxAbsenteisme['agents_absents'] }}</div><div class="label">Absents</div></div>
    <div class="stat-box"><div class="value">{{ $tauxAbsenteisme['taux'] }}%</div><div class="label">Taux</div></div>
    <div class="stat-box"><div class="value">{{ $tauxAbsenteisme['total_jours'] }}</div><div class="label">Jours perdus</div></div>
</div>
<h2>Détail des absences</h2>
<table><thead><tr><th>Réf.</th><th>Agent</th><th>Établissement</th><th>Type</th><th>Circuit</th><th>Début</th><th>Fin</th><th>Jours</th><th>Statut</th></tr></thead><tbody>
@foreach($absences as $a)
<tr><td>{{ $a->reference }}</td><td>{{ $a->user->nom_complet }}</td><td>{{ $a->etablissement->nom }}</td><td>{{ $a->typeAbsence->libelle }}</td><td>{{ ucfirst($a->circuit_validation ?? '—') }}</td><td>{{ $a->date_debut->format('d/m/Y') }}</td><td>{{ $a->date_fin->format('d/m/Y') }}</td><td>{{ $a->nombre_jours }}</td><td><span class="badge badge-{{ $a->statut_badge['color'] }}">{{ $a->statut_badge['label'] }}</span></td></tr>
@endforeach
</tbody></table>
@if(isset($repartitionParType))
<h2>Répartition par type</h2>
<table><thead><tr><th>Type</th><th>Nombre</th><th>Jours</th></tr></thead><tbody>
@foreach($repartitionParType as $r)<tr><td>{{ $r->typeAbsence?->libelle ?? 'Autre' }}</td><td>{{ $r->total }}</td><td>{{ $r->total_jours }}</td></tr>@endforeach
</tbody></table>
@endif
<div class="footer">Document confidentiel — Ministère de l'Éducation Nationale et de l'Alphabétisation — &copy; {{ date('Y') }}</div>
</body></html>
