<?php
namespace XelaxUserForgotPassword\Model;

use Doctrine\ORM\EntityRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\Persisters\Entity\BasicEntityPersister;

/**
 * Repository for ForgotPassword entities
 *
 * @author schurix
 */
class ForgotPasswordRepository extends EntityRepository{

	public function clearExpiredRequests(DateInterval $requestLifetime){
		$time = new DateTime();
		$time->sub($requestLifetime);

		$q = $this->createQueryBuilder('p');
        $q->select()
            ->andWhere('p.createdAt < :created')
            ->setParameter(':created', $time->format('Y-m-d H:i:s'));
		$expired = $q->getQuery()->execute();
		foreach ($expired as $request){
			$this->getEntityManager()->remove($request);
		}
		$this->getEntityManager()->flush();
	}
	
}
