@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Demandes</h1>
        <div class="container">
            <h1 class="mb-5">Demander un acte</h1>
            <div class="row">

                <div class="col-12 col-sm-6 col-md-4 mb-5">
                    <div class="post-entry">
                        <a href="pages/connexion.html" class="post-thumbnail"><img src="img/doc (3).jpeg" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="{{ route('citoyen.demande.naissance') }}">Extrait de naissance</a></h3>
                            <div class="meta">
                                <span>Notes <a href="}"></a></span>
                                <span>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>


                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 mb-5">
                    <div class="post-entry">
                        <a href="pages/connexion.html" class="post-thumbnail"><img src="img/doc.jpeg" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="{{ route('citoyen.demande.mariage') }}">Acte de mariage</a></h3>
                            <div class="meta">
                                <span>Notes <a href="#"></a></span>
                                <span>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>


                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 mb-5">
                    <div class="post-entry">
                        <a href="pages/connexion.html" class="post-thumbnail"><img src="img/doc4.jpeg" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="{{ route('citoyen.demande.deces') }}"> Acte de décès</a></h3>
                            <div class="meta">
                                <span>Notes <a href="#"></a></span>
                                <span>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <h1 class="mb-5">Faire une demande de certificat</h1>

                <div class="col-12 col-sm-6 col-md-4 mb-5">
                    <div class="post-entry">
                        <a href="pages/connexion.html" class="post-thumbnail"><img src="./img/img1.jpeg" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="{{ route('citoyen.demande.certificat_vie') }}">Certificat de vie</a></h3>
                            <div class="meta">
                                <span>Notes <a href="#"></a></span>
                                <span>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 mb-5">
                    <div class="post-entry">
                        <a href="pages/connexion.html" class="post-thumbnail"><img src="../img/dossier 1.jpeg" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="{{ route('citoyen.demande.certificat_residence') }}">Certification de résidence</a></h3>
                            <div class="meta">
                                <span>Notes <a href="#"></a></span>
                                <span>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>


                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 mb-5">
                    <div class="post-entry">
                        <a href="pages/connexion.html" class="post-thumbnail"><img src="img/doc 6.jpeg" alt="Image" class="img-fluid"></a>
                        <div class="post-content-entry">
                            <h3><a href="{{ route('citoyen.demande.certificat_celibat') }}">Certification de célibat </a></h3>
                            <div class="meta">
                                <span>Notes <a href="#"></a></span>
                                <span>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>
                                    <i class="fa fa-star text-warning"></i>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
