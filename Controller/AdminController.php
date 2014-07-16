<?php

namespace SymfonyContrib\Bundle\AlerterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SymfonyContrib\Bundle\AlerterBundle\Entity\Alert;

class AdminController extends Controller
{
    /**
     * Lists all manual alert entries.
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT a
                FROM AlerterBundle:Alert a
                ORDER BY a.dataPoint ASC";
        $alerts = $em->createQuery($dql)->getResult();

        return $this->render('AlerterBundle:Admin:list.html.twig', [
            'alerts' => $alerts,
        ]);
    }

    /**
     * Add/Edit form callback.
     *
     * @param Request $request
     * @param null|int $id
     *
     * @return RedirectResponse|Response
     */
    public function formAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($id) {
            $alert = $em->find('AlerterBundle:Alert', $id);
        } else {
            $alert = new Alert();
        }

        $form = $this->createForm('alerter_alert_form', $alert, [
            'cancel_url' => $this->generateUrl('alerter_alert_admin_list'),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($alert);
            $em->flush();

            // Create a success message for the user.
            $msg = ($id ? 'Updated ' : 'Created ') . $alert->getLabel();
            $this->get('session')->getFlashBag()->add('success', $msg);

            // Redirect to the admin list page.
            return $this->redirect($this->generateUrl('alerter_alert_admin_list'));
        }

        return $this->render('AlerterBundle:Admin:form.html.twig', [
            'alert' => $alert,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * Delete an alert with confirmation.
     *
     * @param string $id ID of alert.
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $alert = $this->getDoctrine()
            ->getRepository('AlerterBundle:Alert')
            ->find($id);

        $options = [
            'message' => 'Are you sure you want to <strong>DELETE "' . $alert->getLabel() . '"</strong>?',
            'warning' => 'This can not be undone!',
            'confirm_button_text' => 'Delete',
            'cancel_link_text' => 'Cancel',
            'confirm_action' => [$this, 'alertDelete'],
            'confirm_action_args' => [
                'alert' => $alert,
            ],
            'cancel_url' => $this->generateUrl('alerter_alert_admin_list'),
        ];

        return $this->forward('ConfirmBundle:Confirm:confirm', ['options' => $options]);
    }

    /**
     * Delete confirmation callback.
     *
     * @param array $args
     *
     * @return RedirectResponse
     */
    public function alertDelete(array $args)
    {
        /** @var Alert $alert */
        $alert = $args['alert'];
        $em    = $this->getDoctrine()->getManager();
        $em->remove($alert);
        $em->flush();

        $msg = 'Deleted ' . $alert->getLabel();
        $this->get('session')->getFlashBag()->add('success', $msg);

        return $this->redirect($this->generateUrl('alerter_alert_admin_list'));
    }
}
