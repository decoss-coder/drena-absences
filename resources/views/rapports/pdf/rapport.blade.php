<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1C2833; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px solid #1B4F72; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #1B4F72; }
        .header p { font-size: 10px; color: #666; }
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 14px; color: #1B4F72; border-bottom: 1px solid #D6EAF8; padding-bottom: 5px; margin-bottom: 10px; }
        .stats { display: table; width: 100%; margin-bottom: 15px; }
        .stat-box { display: table-cell; width: 25%; text-align: center; padding: 10px; background: #F8F9FA; border: 1px solid #DEE2E6; }
        .stat-box .value { font-size: 20px; font-weight: bold; color: #1B4F72; }
        .stat-box .label { font-size: 9px; color: #666; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #1B4F72; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #E8E8E8; }
        tr:nth-child(even) { background: #F8F9FA; }
        .footer { text-align: center; font-size: 8px; color: #999; margin-top: 30px; border-top: 1px solid #DDD; padding-top: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-red { background: #FDEDEC; color: #C0392B; }
        .badge-green { background: #EAFAF1; color: #27AE60; }
        .badge-amber { background: #FEF9E7; color: #F39C12; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT D'ABSENTÉISME</h1>
        <p>République de Côte d'Ivoire — Ministère de l'Éducation Nationale et de l'Alphabétisation</p>
        <p>{{ $drena ? $drena->nom : 'Vue nationale — Toutes les DRENA' }} | Période : {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="section">
        <h2>Indicateurs clés</h2>
        <div class="stats">
            <div class="stat-box"><div class="value">{{ number_format($tauxAbsenteisme['total_agents']) }}</div><div class="label">Agents</div></div>
            <div class="stat-box"><div class="value">{{ $tauxAbsenteisme['agents_absents'] }}</div><div class="label">Absents</div></div>
            <div class="stat-box"><div class="value">{{ $tauxAbsenteisme['taux'] }}%</div><div class="label">Taux d'absentéisme</div></div>
            <div class="stat-box"><div class="value">{{ $tauxAbsenteisme['total_jours'] }}</div><div class="label">Jours total</div></div>
        </div>
    </div>

    @if($topEtablissements && $topEtablissements->count() > 0)
    <div class="section">
        <h2>Top 10 — Établissements les plus touchés</h2>
        <table>
            <thead><tr><th>#</th><th>Établissement</th><th>Absences</th><th>Jours total</th></tr></thead>
            <tbody>
                @foreach($topEtablissements as $i => $e)
                <tr><td>{{ $i + 1 }}</td><td>{{ $e->nom }}</td><td>{{ $e->total_absences }}</td><td>{{ $e->total_jours }}j</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($topAgents && $topAgents->count() > 0)
    <div class="section">
        <h2>Top 10 — Agents les plus absents</h2>
        <table>
            <thead><tr><th>#</th><th>Matricule</th><th>Nom & Prénoms</th><th>Spécialité</th><th>Total jours</th></tr></thead>
            <tbody>
                @foreach($topAgents as $i => $a)
                <tr><td>{{ $i + 1 }}</td><td>{{ $a->matricule }}</td><td>{{ $a->nom_complet }}</td><td>{{ $a->specialite ?? '—' }}</td><td><span class="badge {{ $a->total_jours > 20 ? 'badge-red' : 'badge-amber' }}">{{ $a->total_jours }}j</span></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($repartitionParType && $repartitionParType->count() > 0)
    <div class="section">
        <h2>Répartition par type d'absence</h2>
        <table>
            <thead><tr><th>Type</th><th>Nombre d'absences</th><th>Total jours</th></tr></thead>
            <tbody>
                @foreach($repartitionParType as $r)
                <tr><td>{{ $r->typeAbsence->libelle ?? 'N/A' }}</td><td>{{ $r->total }}</td><td>{{ $r->total_jours }}j</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Document généré automatiquement par l'application DRENA Absences — MENA Côte d'Ivoire</p>
        <p>Ce document est confidentiel et réservé à un usage interne.</p>
    </div>
</body>
</html>
