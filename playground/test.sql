SELECT reunioes.id, reunioes.motivo, reunioes.data, reunioes.hora_inicio, reunioes.hora_fim, reunioes.organizador, salas.nome 
        AS sala, salas.url_imagem,
        (
            SELECT GROUP_CONCAT(participantes_reunioes.nome_participante SEPARATOR \', \')
            FROM participantes_reunioes
            WHERE participantes_reunioes.id_reuniao = reunioes.id
        ) AS guests
        FROM reunioes
        JOIN salas ON reunioes.id_sala = salas.id
        ORDER BY reunioes.data DESC, reunioes.hora_inicio ASC