<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function listeInfosSorties()
    {

        $entityManager = $this->getEntityManager();
        $dql = "SELECT s.id as sortieID, s.nom, s.dateHeureDebut, s.dateLimiteInscription, s.duree, s.nbInscriptionsMax,  e.libelle, e.id,  p.pseudo, p.id as organisateurId
        FROM App\Entity\Sortie s 
        LEFT JOIN App\Entity\Etat e
        WITH e.id = s.etat
        LEFT JOIN App\Entity\Participant p
        WITH p.id = s.organisateur";
        $query = $entityManager->createQuery($dql);
        $results = $query->getResult();
        return $results;
    }


}