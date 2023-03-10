<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Filtre\FiltreClass;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function listeInfosSorties(FiltreClass $data, Participant $user)
    {
        $queryBuilder=$this->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->addSelect('e')
            ->leftJoin('s.inscrits', 'i')
            ->addSelect('i')
            ->leftJoin('s.siteOrganisateur', 'c')
            ->addSelect('c')
            ->orderBy('s.dateHeureDebut', 'DESC');

        //filtre recherche par nom partiel ou total
        if (!empty($data->motCle)){
            $queryBuilder
                ->andWhere('s.nom LIKE :nom' )
                ->setParameter('nom', '%'.$data->motCle.'%');
        }
        //filtre par campus
        if (!empty($data->campus)){
           $queryBuilder
               ->andWhere('s.siteOrganisateur = :campus')
               ->setParameter('campus', $data->campus);

        }

        if (!empty($data->dateMini)){
            $queryBuilder
                ->andWhere('s.dateHeureDebut >:dateMini')
                ->setParameter('dateMini', $data->dateMini);
        }

        if (!empty($data->dateMax)){
            $queryBuilder
                ->andWhere('s.dateLimiteInscription <:dateMax')
                ->setParameter('dateMax', $data->dateMax);
        }

        if (!empty($data->sortiesFinies)){
            $queryBuilder
                ->andWhere('e.libelle = :name')
                ->setParameter('name', 'Cl??tur??e');
        }

        if (!empty($data->estOrganisee)){
            $queryBuilder
                ->andWhere('s.organisateur = :name')
                ->setParameter('name', $user);
        }

        if (!empty($data->estInscrit)){
            $queryBuilder
                ->andWhere(':name MEMBER OF s.inscrits')
                ->setParameter('name', $user);
        }

        if (!empty($data->nonInscrit)){
            $queryBuilder
                ->andWhere(':name NOT MEMBER OF s.inscrits')
                ->setParameter('name', $user);
        }


        return $queryBuilder->getQuery()->getResult();

    }

    public function listeSorties(){
        $qb = $this->createQueryBuilder('s')
            ->select('s','i','e')
            ->leftJoin('s.inscrits', 'i')
            ->leftJoin('s.etat', 'e');
            
            return $qb->getQuery()->getResult();
    }

    public function changerEtat(Etat $etatId, Sortie $sortie){

        $sortie->setEtat($etatId);
        $this->save($sortie, true);

    }


}