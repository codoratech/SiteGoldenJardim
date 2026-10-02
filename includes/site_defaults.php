<?php
// Rafael's public content is the fallback; saved admin content takes precedence.
function site_defaults() {
    return json_decode(<<<'JSON'
{
  "sections": {
    "services": {
      "pt": {
        "title": "SISTEMAS\nCENTRAIS",
        "subtitle": "Especialidades conduzidas por Rodrigo & Fernanda Câmara",
        "eyebrow": ""
      },
      "en": {
        "title": "CORE\nSYSTEMS",
        "subtitle": "Specialty services led by Rodrigo & Fernanda Câmara",
        "eyebrow": ""
      }
    },
    "gallery": {
      "pt": {
        "title": "Projetos assinados",
        "subtitle": "Uma seleção de transformações entregues em Americana e região.",
        "eyebrow": "Portfólio"
      },
      "en": {
        "title": "Signature projects",
        "subtitle": "Selected transformations delivered across Americana region.",
        "eyebrow": "Portfolio"
      }
    }
  },
  "services": [
    {
      "icon": "sprout",
      "img": "assets/rafael-servico-1-b8253284b9a9.jpg",
      "pt": [
        "Paisagismo",
        "Projetos autorais de paisagismo, da concepção 3D à execução final."
      ],
      "en": [
        "Landscaping",
        "Signature landscape design — from 3D vision to flawless delivery."
      ]
    },
    {
      "icon": "droplet",
      "img": "assets/rafael-servico-2-4028ff8f2bc1.jpg",
      "pt": [
        "Manutenção",
        "Contratos mensais para condomínios e residências de alto padrão."
      ],
      "en": [
        "Maintenance",
        "Monthly contracts for premium condominiums and residences."
      ]
    },
    {
      "icon": "pruning",
      "img": "assets/rafael-servico-3-d941886adbc1.jpg",
      "pt": [
        "Poda Técnica",
        "Poda de árvores e jardins com técnicas avançadas de condução."
      ],
      "en": [
        "Expert Pruning",
        "Tree and garden pruning with advanced shaping techniques."
      ]
    },
    {
      "icon": "tree",
      "img": "assets/rafael-servico-4-acd84de31fe7.jpg",
      "pt": [
        "Jardinagem",
        "Cuidado contínuo, adubação inteligente e saúde vegetal completa."
      ],
      "en": [
        "Gardening",
        "Ongoing care, smart fertilization and full plant health."
      ]
    },
    {
      "icon": "building",
      "img": "assets/rafael-servico-5-03f8ad9d6c5d.jpg",
      "pt": [
        "Comercial",
        "Soluções escaláveis para empresas, lobbies e áreas corporativas."
      ],
      "en": [
        "Commercial",
        "Scalable solutions for offices, lobbies and corporate sites."
      ]
    },
    {
      "icon": "compass",
      "img": "assets/rafael-servico-6-31b0e8a272eb.jpg",
      "pt": [
        "Consultoria",
        "Diagnóstico, plano de manejo e curadoria botânica especializada."
      ],
      "en": [
        "Consulting",
        "Diagnostics, management plans and curated botanical advisory."
      ]
    }
  ],
  "projects": [
    {
      "img": "assets/rafael-projeto-1-5076d9ab9003.jpg",
      "pt": {
        "title": "Jardim Frontal",
        "description": "",
        "category": "Residencial",
        "location": "",
        "alt": "Jardim Frontal"
      },
      "en": {}
    },
    {
      "img": "assets/rafael-projeto-2-e6bb8b7ca95d.jpg",
      "pt": {
        "title": "Área de Lazer",
        "description": "",
        "category": "Residencial",
        "location": "",
        "alt": "Área de Lazer"
      },
      "en": {}
    },
    {
      "img": "assets/rafael-projeto-3-35bce92e1814.jpg",
      "pt": {
        "title": "Entrada · Parque das Oliveiras",
        "description": "",
        "category": "Condomínio",
        "location": "",
        "alt": "Entrada · Parque das Oliveiras"
      },
      "en": {}
    },
    {
      "img": "assets/rafael-projeto-4-1ac077b88f0d.jpg",
      "pt": {
        "title": "Entrada · Villagio Azul",
        "description": "",
        "category": "Condomínio",
        "location": "",
        "alt": "Entrada · Villagio Azul"
      },
      "en": {}
    }
  ]
}
JSON, true, 512, JSON_THROW_ON_ERROR);
}
