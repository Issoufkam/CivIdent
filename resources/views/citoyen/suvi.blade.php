@extends('layouts.app')

@section('content')

    <style>
        :root {
            --primary: #0056b3;
            --primary-light: rgba(0, 86, 179, 0.1);
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--gray-100);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary);
        }

        .status-card {
            border: none;
            border-radius: 15px;
            transition: var(--transition);
            height: 100%;
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-pending { background-color: var(--warning); }
        .status-approved { background-color: var(--success); }
        .status-rejected { background-color: var(--danger); }

        .request-card {
            border: none;
            border-radius: 15px;
            transition: var(--transition);
            cursor: pointer;
        }

        .request-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .timeline {
            position: relative;
            padding-left: 45px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            border-left: 2px solid var(--gray-300);
            padding-left: 20px;
        }

        .timeline-item:last-child {
            border-left: none;
        }

        .timeline-dot {
            position: absolute;
            left: -11px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--primary);
        }

        .timeline-date {
            font-size: 0.85rem;
            color: var(--gray-600);
        }

        .document-item {
            transition: var(--transition);
            border-radius: 10px;
        }

        .document-item:hover {
            background-color: var(--gray-200);
        }

        @media (max-width: 768px) {
            .status-cards {
                margin-bottom: 1.5rem;
            }
            
            .timeline {
                padding-left: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-file-earmark-text me-2"></i>CitizenDocs
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Mes Demandes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Nouvelle Demande</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>Thomas Dubois
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mon Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Status Cards -->
        <div class="row status-cards g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="status-card card bg-white p-4">
                    <h6 class="text-muted mb-3">Total des Demandes</h6>
                    <h2 class="mb-0">4</h2>
                    <small class="text-muted">Depuis votre inscription</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="status-card card bg-white p-4">
                    <h6 class="text-muted mb-3">En Attente</h6>
                    <h2 class="mb-0">1</h2>
                    <small class="text-warning">En cours de traitement</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="status-card card bg-white p-4">
                    <h6 class="text-muted mb-3">Approuvées</h6>
                    <h2 class="mb-0">2</h2>
                    <small class="text-success">Prêtes à être récupérées</small>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="status-card card bg-white p-4">
                    <h6 class="text-muted mb-3">Rejetées</h6>
                    <h2 class="mb-0">1</h2>
                    <small class="text-danger">Nécessitent une action</small>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="card bg-white shadow-sm">
            <div class="card-header bg-white py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Demandes</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Rechercher...">
                        </div>
                        <button class="btn btn-primary">
                            <i class="bi bi-plus-lg me-2"></i>Nouvelle Demande
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="requestsList">
                    <!-- Request items will be dynamically populated -->
                </div>
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div class="modal fade" id="requestDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de la Demande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informations Générales</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Numéro</th>
                                    <td id="modalRequestId"></td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td id="modalDocumentType"></td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td id="modalRequestDate"></td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td id="modalStatus"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Documents Fournis</h6>
                            <div id="modalDocuments" class="list-group list-group-flush">
                                <!-- Documents will be populated here -->
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6 class="mb-3">Historique</h6>
                        <div class="timeline" id="modalTimeline">
                            <!-- Timeline items will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Using the mock data from the previous example
        const mockRequests = [
            {
                id: "REQ-2023-1254",
                citizen: {
                    id: "CIT-2345",
                    name: "Thomas Dubois",
                    email: "thomas.dubois@example.com",
                    phone: "06 12 34 56 78",
                    address: "15 Rue des Lilas, 75020 Paris",
                    dateOfBirth: "1985-04-12"
                },
                documentType: "Extrait de naissance",
                requestDate: "2023-05-15",
                status: "pending",
                urgent: true,
                notes: "Le demandeur a besoin du document rapidement pour une démarche administrative.",
                documents: [
                    {
                        name: "Pièce d'identité",
                        type: "image/jpeg",
                        size: "1.2 MB",
                        uploadDate: "2023-05-15"
                    },
                    {
                        name: "Justificatif de domicile",
                        type: "application/pdf",
                        size: "0.8 MB",
                        uploadDate: "2023-05-15"
                    }
                ],
                history: [
                    {
                        date: "2023-05-15 10:23",
                        action: "Soumission de la demande",
                        agent: "-",
                        comment: "Demande créée par le citoyen"
                    },
                    {
                        date: "2023-05-15 14:45",
                        action: "Documents vérifiés",
                        agent: "Marie Laurent",
                        comment: "Documents conformes"
                    }
                ]
            }
            // ... other requests
        ];

        function getStatusBadge(status) {
            const badges = {
                pending: '<span class="badge bg-warning">En attente</span>',
                approved: '<span class="badge bg-success">Approuvée</span>',
                rejected: '<span class="badge bg-danger">Rejetée</span>'
            };
            return badges[status] || '';
        }

        function populateRequestsList() {
            const requestsList = document.getElementById('requestsList');
            requestsList.innerHTML = '';

            mockRequests.forEach(request => {
                const listItem = document.createElement('div');
                listItem.className = 'list-group-item request-card';
                listItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div>
                            <h6 class="mb-1">${request.documentType}</h6>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-calendar me-2"></i>${request.requestDate}
                                ${request.urgent ? '<span class="badge bg-danger ms-2">Urgent</span>' : ''}
                            </p>
                        </div>
                        <div class="text-end">
                            ${getStatusBadge(request.status)}
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="showRequestDetails('${request.id}')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                `;
                requestsList.appendChild(listItem);
            });
        }

        function showRequestDetails(requestId) {
            const request = mockRequests.find(r => r.id === requestId);
            if (!request) return;

            // Populate modal fields
            document.getElementById('modalRequestId').textContent = request.id;
            document.getElementById('modalDocumentType').textContent = request.documentType;
            document.getElementById('modalRequestDate').textContent = request.requestDate;
            document.getElementById('modalStatus').innerHTML = getStatusBadge(request.status);

            // Populate documents
            const documentsContainer = document.getElementById('modalDocuments');
            documentsContainer.innerHTML = request.documents.map(doc => `
                <div class="document-item list-group-item d-flex justify-content-between align-items-center p-3">
                    <div>
                        <i class="bi bi-file-earmark me-2"></i>
                        <span>${doc.name}</span>
                        <small class="text-muted d-block">${doc.size} - ${doc.uploadDate}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            `).join('');

            // Populate timeline
            const timelineContainer = document.getElementById('modalTimeline');
            timelineContainer.innerHTML = request.history.map(item => `
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-date">${item.date}</div>
                    <div class="fw-bold">${item.action}</div>
                    <div class="text-muted">${item.comment}</div>
                    ${item.agent !== '-' ? `<small class="text-primary">Par: ${item.agent}</small>` : ''}
                </div>
            `).join('');

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('requestDetailsModal'));
            modal.show();
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', () => {
            populateRequestsList();
        });
    </script>
</body>

@endsection